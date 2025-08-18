import axios from 'axios';
import { defineStore } from 'pinia';

export const usePropertyStore = defineStore('property', {
  state: () => ({
    properties: [],
    selectedPropertyDetails: null,
    detailsLoading: false,
    availableFilters: [],
    appliedFilters: {},
    filters: {
      page: 1,
      sortBy: 0,
      search: '',
      listingType: 'sale',
      nh: ['Brampton'],
    },
    loading: false,
    hasMorePages: true,
    error: null,
  }),
  actions: {
    // API se main property listings ko fetch karne ka action
    async fetchProperties(append = false) {
      if (this.loading || (!this.hasMorePages && append)) {
        return;
      }
      
      this.loading = true;
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

        const response = await axios.get(`https://api.vow.v1.agentroof.ca/api/get-filter-properties?${params.toString()}`);
        
        const newProperties = response.data.data.data.map(property => {
          let mainImage = 'https://placehold.co/100x100'; 
          
          if (property.images && property.images.directory) {
            const imagePrefix = 'Photo';
            mainImage = `${property.images.directory}${imagePrefix}${property.ml_num}-1.jpeg`;
          }
          
          return {
            label: property.sale_lease,
            labelColor: property.sale_lease === 'For Sale' ? '#1F7B4A' : '#FE043C',
            title: property.addr,
            address: property.city + ', ' + property.province,
            price: `$${property.listed_price.toLocaleString()}`,
            beds: property.bed,
            baths: property.bath,
            size: property.sqft ? property.sqft + ' Sqft' : 'N/A',
            type: property.building_type,
            mainImage: mainImage,
            
            images: [mainImage], 
            status: property.sale_lease,
            addedDate: new Date(property.added_ts).toLocaleDateString(),
            listedPrice: `$${property.listed_price.toLocaleString()}`,
            mlsNumber: property.ml_num,
            propertyType: property.prop_type,
            buildingStyle: property.building_type,
            sizeSqft: property.sqft,
            lotDepth: 'N/A',
            listedBy: property.brokerage,
            source: 'VOW',
            age: 'N/A',
            area: property.area,
            community: property.community,
            bedrooms: property.bed,
            bathrooms: property.bath,
            kitchen: 'N/A',
            rooms: 'N/A',
            roomPlus: 'N/A',
            denFamilyRoom: 'N/A',
            centralVac: 'N/A',
            unitExposure: 'N/A',
            airConditioning: 'N/A',
            firePlace: 'N/A',
            basement: 'N/A',
            heating: 'N/A',
            fuelHeating: 'N/A',
            waterSupply: 'N/A',
            exterior: 'N/A',
            slug: property.slug,
          };
        });
        
        if (append) {
          // Naya data purane data ke saath judega
          this.properties.push(...newProperties);
        } else {
          // Purana data replace ho jayega
          this.properties = newProperties;
        }

        const currentPage = response.data.data.current_page;
        const lastPage = response.data.data.last_page;
        this.hasMorePages = currentPage < lastPage;

      } catch (err) {
        this.error = 'Failed to fetch properties.';
        console.error('Error fetching property data:', err);
      } finally {
        this.loading = false;
      }
    },
    
    // API se dynamic filter options ko fetch karne ka action
    async fetchFilterOptions() {
      try {
        const response = await axios.get(`https://api.vow.v1.agentroof.ca/api/get-crm-filters?from=listing&listing=${this.filters.listingType}`);
        this.availableFilters = response.data.data;
      } catch (err) {
        console.error('Error fetching filter options:', err);
      }
    },

    // Filter change hone par data ko update karne ka action
    async applyFilters(newFilters) {
      this.appliedFilters = newFilters;
      this.filters.page = 1; // Filter change hone par page 1 par reset ho
      this.hasMorePages = true;
      await this.fetchProperties(false); // Data replace karein
    },

    // Infinite scroll ke liye naya data fetch karne ka action
    async loadMoreProperties() {
      if (this.hasMorePages && !this.loading) {
        this.filters.page++;
        await this.fetchProperties(true); // Data append karein
      }
    },

    // Listing type change hone par
    async handleListingTypeChange() {
      this.filters.page = 1; // Listing type change hone par page 1 par reset ho
      this.hasMorePages = true;
      this.appliedFilters = {};
      await this.fetchFilterOptions();
      await this.fetchProperties(false); // Data replace karein
    },

    // Clear All filters
    async clearAllFilters() {
      this.filters.search = '';
      this.filters.page = 1;
      this.appliedFilters = {};
      this.hasMorePages = true;
      await this.fetchProperties(false); // Data replace karein
    },

  async fetchPropertyDetails(slug) {
    this.detailsLoading = true;
    this.selectedPropertyDetails = null; // Purana data clear karein
    try {
      // Payload mein 'value: slug' ko include karein
      const payload = {
        value: slug,
        view_type: 'crm',
      };
      
      const response = await axios.post('https://api.vow.v1.agentroof.ca/api/get-property-details', payload);
      this.selectedPropertyDetails = response.data.data;
    } catch (err) {
      console.error('Error fetching property details:', err);
      // Agar API se error response aaye, to selectedPropertyDetails ko null hi rehne dein
      this.selectedPropertyDetails = null; 
    } finally {
      this.detailsLoading = false;
    }
  },

  },
});
