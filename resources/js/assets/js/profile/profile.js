import axios from "axios";
import Swal from "sweetalert2";

export default {
  data() {
    return {
      userRole: null,
      form: {
        first_name: "",
        email1: "",
        email2: "",
        email3: "",
        phone1: "",
        phone2: "",
        phone3: "",
        password: "",
        role: "",
      },
      
      roles: [
        { value: "superadmin", label: "Superadmin" },
        { value: "admin", label: "Admin" },
        { value: "user", label: "User" },
        { value: "custom", label: "Custom" },
      ],
    };
  },
  computed: {
    filteredRoles() {
      if (this.userRole === "superadmin") {
        return this.roles.filter((role) => role.value === "admin");
      } else if (this.userRole === "admin") {
        return [];
      }
      return [];
    },
    showRoleField() {
      return this.userRole === "superadmin";
    },
  },

  methods: {
    async fetchUserRole() {
      try {
        const token = localStorage.getItem("auth_token");
        if (!token) {
          Swal.fire({
            icon: "error",
            title: "Unauthorized",
            text: "No authentication token found. Please log in again.",
          });
          return;
        }

        const response = await axios.get("/api/get-role", {
          headers: {
            Authorization: `Bearer ${token}`,
            Accept: "application/json",
          },
        });

        this.userRole = response.data.role;
      } catch (error) {
        console.error("Error fetching user role:", error);
        Swal.fire({
          icon: "error",
          title: "Error",
          text: "Failed to fetch user role. Please try again.",
        });
      }
    },
    async handleSubmit() {
      try {
        const response = await axios.post("/api/form-submit", this.form, {
          headers: {
            Authorization: `Bearer ${localStorage.getItem("auth_token")}`,
            Accept: "application/json",
          },
        });

        Swal.fire({
          icon: "success",
          title: "Form submitted successfully!",
          text: "Your data has been successfully saved.",
        });
      } catch (error) {
        Swal.fire({
          icon: "error",
          title: "Error",
          text: "There was an error submitting the form. Please try again.",
        });
      }
    },
  },
  mounted() {
    this.fetchUserRole();
  },
};
