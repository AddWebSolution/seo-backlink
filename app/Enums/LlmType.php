<?php

namespace App\Enums;

enum LlmType: string
{
    case GPT = 'gpt';
    case GEMINI = 'gemini';
    case COHERE = 'cohere';
}
