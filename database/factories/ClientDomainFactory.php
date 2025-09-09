<?php

namespace Database\Factories;

use App\Modules\Report\Models\Report;
use App\Modules\ClientDomain\Models\ClientDomain;
use Illuminate\Database\Eloquent\Factories\Factory;
use App\Modules\Backlinkreport\Models\Backlinkreport;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Model>
 */
class ClientDomainFactory extends Factory
{
    protected $model = ClientDomain::class;
    
    // Static variable to track reports and domains
    private static $reports = [];
    private static $currentReportIndex = 0;
    private static $domainsPerReport = 0;
    
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        $companyName = $this->faker->company();

        $cleanCompanyName = strtolower(preg_replace('/[^a-zA-Z0-9]/', '', $companyName));

        // Create target URL based on company name
        $targetUrl = "https://www.{$cleanCompanyName}.com";
        
        return [
            'source_url' => $this->faker->url,
            'title' => $companyName,
            'target_url' => $targetUrl,
            'domain_authority' => $this->faker->numberBetween(10, 100),
            'domain_rating' => $this->faker->numberBetween(10, 100),
            'organic_traffic' => $this->faker->numberBetween(1000, 50000),
            'price_ne' => $this->faker->randomFloat(2, 50, 500),
            'price_gp' => $this->faker->randomFloat(2, 100, 1000),
            'total_price' => $this->faker->randomFloat(2, 150, 1500),
            'turnaround_time' => $this->faker->numberBetween(1, 10),
            'status' => $this->faker->randomElement(['1', '2']),
            'approval_status' => $this->faker->numberBetween(1, 3),
            'country' => $this->faker->countryCode,
            'anchor_text' => $this->faker->sentence(5),
            'special_requirements' => $this->faker->paragraph(),
            'price' => $this->faker->randomFloat(2, 200, 2000),
        ];
    }

    /**
     * Configure the model factory
     */
    public function configure()
    {
        return $this->afterCreating(function (ClientDomain $clientDomain) {
            // Force assign attributes if mass assignment fails
            $attributes = $this->definition();
            foreach ($attributes as $key => $value) {
                if (!isset($clientDomain->$key) || $clientDomain->$key === null) {
                    $clientDomain->$key = $value;
                }
            }
            $clientDomain->saveQuietly();

            // Initialize reports if not already done
            if (empty(self::$reports)) {
                $this->initializeReports();
            }

            // Get current report
            $currentReport = self::$reports[self::$currentReportIndex];
            
            // Create 10 backlink reports for this domain
            for ($i = 0; $i < 10; $i++) {
                Backlinkreport::create([
                    'report_id' => $currentReport->id,
                    'target_url' => $clientDomain->target_url,
                    'domain' => $clientDomain->title,
                    'from_domain' => $this->faker->domainName,
                    'rank' => $this->faker->numberBetween(1, 1000),
                    'domain_rank' => $this->faker->numberBetween(10, 100),
                    'spam_score' => $this->faker->numberBetween(0, 100),
                    'do_follow' => $this->faker->boolean(70),
                    'tier' => $this->faker->numberBetween(1, 3),
                    'score' => $this->faker->numberBetween(1, 100),
                    'is_broken' => $this->faker->boolean(10),
                    'url' => $this->faker->url,
                    'from_url' => $this->faker->url,
                    'anchor' => $this->faker->sentence(3),
                    'status' => $this->faker->randomElement(['accepted', 'rejected']),
                    'reason' => $this->faker->sentence(),
                    'key_highlights' => json_encode([$this->faker->sentence(), $this->faker->sentence()]),
                    'first_seen' => now()->subDays(rand(1, 100)),
                    'last_seen' => now(),
                    'link_type' => $this->faker->word(),
                    'page_title' => $this->faker->sentence(),
                ]);
            }

            // Update report total_backlink count
            $currentReport->increment('total_backlink', 10);
            $currentReport->increment('domain_count', 1);

            // Move to next report after 3 domains
            self::$domainsPerReport++;
            if (self::$domainsPerReport >= 3) {
                self::$currentReportIndex++;
                self::$domainsPerReport = 0;
            }
        });
    }

    /**
     * Initialize the 3 reports that will contain the domains
     */
    private function initializeReports()
    {
        for ($i = 0; $i < 3; $i++) {
            self::$reports[] = Report::create([
                'run_id' => 'run_' . uniqid(),
                'domain_count' => 0, // Will be incremented as domains are added
                'total_backlink' => 0, // Will be incremented as backlinks are added
                'accepted_backlinks' => 0,
                'rejected_backlinks' => 0,
                'run_at' => now(),
            ]);
        }
    }

    /**
     * Create the complete structure: 3 reports with 3 domains each
     */
    public static function createCompleteStructure()
    {
        // Reset static variables
        self::$reports = [];
        self::$currentReportIndex = 0;
        self::$domainsPerReport = 0;

        // Create 9 domains (3 for each report)
        ClientDomain::factory()->count(9)->create();

        return self::$reports;
    }
}