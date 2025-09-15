import { createFetch } from "@vueuse/core";
import { destr } from "destr";
import { unref } from "vue";

export const useApi = createFetch({
  baseUrl: import.meta.env.VITE_API_BASE_URL || "/api",
  fetchOptions: {
    headers: {
      Accept: "application/json",
    },
  },
  options: {
    refetch: true,
    async beforeFetch({ options }) {
      const accessToken = useCookie("accessToken").value;
      if (accessToken) {
        options.headers = {
          ...options.headers,
          Authorization: `Bearer ${accessToken}`,
        };
      }
         if (
           !options.skipJsonTransform &&
           options.body &&
           typeof options.body === "object"
         ) {
           const rawBody = unref(options.body);
           options.body = JSON.stringify(rawBody);
           options.headers = {
             ...options.headers,
             Accept: "application/json",
             "Content-Type": "application/json",
           };
         }

      return { options };
    },
    afterFetch(ctx) {
      const { data, response } = ctx;

      // Parse data if it's JSON
      let parsedData = null;
      try {
        parsedData = destr(data);
      } catch (error) {
        console.error(error);
      }

      return { data: parsedData, response };
    },
  },
});
