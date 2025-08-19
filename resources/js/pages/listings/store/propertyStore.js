/* eslint-disable no-unused-vars */
/* eslint-disable no-useless-catch */
import axios from "axios";
import { defineStore } from "pinia";

export const usePropertyStore = defineStore("property", {
  state: () => ({
    properties: [],
    loadingInitial: false, // page 1 ke liye
    loadingMore: false, // infinite scroll ke liye
    selectedPropertyDetails: null,
    detailsLoading: false,
    availableFilters: [],
    appliedFilters: {},
    filters: {
      page: 1,
      sortBy: 0,
      search: "",
      listingType: "sale",
      nh: ["Brampton"],
    },
    hasMorePages: true,
    error: null,
  }),

  actions: {
    // ✅ Property map karne ka helper
    mapProperty(property) {
      let mainImage = "https://placehold.co/100x100";
      if (property.images && property.images.directory) {
        const imagePrefix = "Photo";
        mainImage = `${property.images.directory}${imagePrefix}${property.ml_num}-1.jpeg`;
      }

      return {
        label: property.sale_lease,
        labelColor: property.sale_lease === "For Sale" ? "#1F7B4A" : "#FE043C",
        title: property.addr,
        address: property.city + ", " + property.province,
        price: `$${property.listed_price.toLocaleString()}`,
        beds: property.bed,
        baths: property.bath,
        size: property.sqft ? property.sqft + " Sqft" : "N/A",
        type: property.building_type,
        mainImage,
        images: [mainImage],
        status: property.sale_lease,
        addedDate: new Date(property.added_ts).toLocaleDateString(),
        listedPrice: `$${property.listed_price.toLocaleString()}`,
        mlsNumber: property.ml_num,
        propertyType: property.prop_type,
        buildingStyle: property.building_type,
        sizeSqft: property.sqft,
        lotDepth: "N/A",
        listedBy: property.brokerage,
        source: "VOW",
        age: "N/A",
        area: property.area,
        community: property.community,
        bedrooms: property.bed,
        bathrooms: property.bath,
        slug: property.slug,
      };
    },

    // ✅ Main fetch (page 1 ya append dono handle karega)
    async fetchProperties(append = false) {
      if (!append) this.loadingInitial = true;
      this.error = null;

      try {
        const params = new URLSearchParams({
          page: this.filters.page,
          sort_by: this.filters.sortBy,
          search: this.filters.search,
          listing_type: this.filters.listingType,
          nh: JSON.stringify(this.filters.nh),
          ...this.appliedFilters,
        });

        const response = await axios.get(
          `https://api.vow.v1.agentroof.ca/api/get-filter-properties?${params.toString()}`
        );

        const newProperties = response.data.data.data.map(this.mapProperty);

        if (append) {
          this.properties = [...this.properties, ...newProperties];
        } else {
          this.properties = newProperties;
        }

        const currentPage = response.data.data.current_page;
        const lastPage = response.data.data.last_page;
        this.hasMorePages = currentPage < lastPage;
      } catch (err) {
        this.error = "Failed to fetch properties.";
        console.error("Error fetching property data:", err);
      } finally {
        this.loadingInitial = false;
      }
    },

    // ✅ Infinite scroll
    async loadMoreProperties() {
      if (!this.hasMorePages || this.loadingMore) return;

      this.loadingMore = true;
      this.filters.page++;

      try {
        await this.fetchProperties(true); // append mode
      } catch (err) {
        console.error("Error loading more properties:", err);
      } finally {
        this.loadingMore = false;
      }
    },

    // ✅ Filters
    async fetchFilterOptions() {
      try {
        const response = await axios.get(
          `https://api.vow.v1.agentroof.ca/api/get-crm-filters?from=listing&listing=${this.filters.listingType}`
        );
        this.availableFilters = response.data.data;
      } catch (err) {
        console.error("Error fetching filter options:", err);
      }
    },

    async applyFilters(newFilters) {
      this.appliedFilters = newFilters;
      this.filters.page = 1;
      this.hasMorePages = true;
      await this.fetchProperties(false);
    },

    async handleListingTypeChange() {
      this.filters.page = 1;
      this.hasMorePages = true;
      this.appliedFilters = {};
      await this.fetchFilterOptions();
      await this.fetchProperties(false);
    },

    async clearAllFilters() {
      this.filters.search = "";
      this.filters.page = 1;
      this.appliedFilters = {};
      this.hasMorePages = true;
      await this.fetchProperties(false);
    },

    // ✅ Property details
    async fetchPropertyDetails(slug) {
      this.detailsLoading = true;
      this.selectedPropertyDetails = null;
      try {
        const payload = { value: slug, view_type: "crm" };
        const response = await axios.post(
          "https://api.vow.v1.agentroof.ca/api/get-property-details",
          payload
        );
        this.selectedPropertyDetails = response.data.data;
      } catch (err) {
        console.error("Error fetching property details:", err);
        this.selectedPropertyDetails = null;
      } finally {
        this.detailsLoading = false;
      }
    },
  },
});
