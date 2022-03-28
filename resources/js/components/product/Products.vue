<template>
  <section class="content">
    <div class="container-fluid">
      <div class="row">
        <div class="col-12">
          <div class="card">
            <div class="card-header">
              <h3 class="card-title">Face Recognition</h3>
              <div class="card-tools" hidden>
                <button
                  type="button"
                  class="btn btn-sm btn-primary"
                  @click="newModal"
                >
                  <i class="fa fa-plus-square"></i>
                  Add New
                </button>
              </div>
            </div>
            <!-- /.card-header -->
            <div class="card-body table-responsive p-0">
              <table class="table table-hover">
                <thead>
                  <tr>
                    <th>ID</th>
                    <th>Name</th>
                    <th>Face image</th>
                    <th>Accuracy</th>
                    <th>Description</th>
                    <th>Category</th>
                    <th>Clock in</th>
                    <th>Action</th>
                  </tr>
                </thead>
                <tbody>
                  <tr
                    v-for="(product, index) in products.data"
                    :key="product.id"
                  >
                    <td>{{ index + 1 }}</td>
                    <td>{{ product.name }}</td>
                    <td>
                      <img
                        :src="'data:image/png;base64,' + product.photo"
                        width="100"
                        alt="product"
                      />
                    </td>
                    <td>{{ product.price * 100 }}</td>
                    <td>{{ product.description | truncate(30, "...") }}</td>
                    <td>{{ product.category.name }}</td>
                    <td>{{ product.created_at | myDate }}</td>
                    <td>
                      <a
                        v-if="$gate.isAdmin()"
                        href="#"
                        @click="editModal(product)"
                      >
                        <i class="fa fa-2x fa-edit blue"></i>
                      </a>
                      &nbsp;
                      <a
                        v-if="$gate.isAdmin()"
                        href="#"
                        @click="
                          deleteProduct(
                            product.id,
                            product.camera_id,
                            product.created_at
                          )
                        "
                      >
                        <i class="fa fa-2x fa-trash red"></i>
                      </a>
                      <a
                        v-if="$gate.isUser()"
                        href="#"
                        @click="report(product.image_id, product.id)"
                      >
                        <i class="fas fa-2x fa-user-times"></i>
                      </a>
                    </td>
                  </tr>
                </tbody>
              </table>
            </div>
            <!-- /.card-body -->
            <div class="card-footer">
              <pagination
                :data="products"
                @pagination-change-page="getResults"
              ></pagination>
            </div>
          </div>
          <!-- /.card -->
        </div>
      </div>
      <!-- Modal -->
      <div
        class="modal fade"
        id="addNew"
        tabindex="-1"
        role="dialog"
        aria-labelledby="addNew"
        aria-hidden="true"
      >
        <div class="modal-dialog" role="document">
          <div class="modal-content">
            <div class="modal-header">
              <h5 class="modal-title" v-show="!editmode">Create New Product</h5>
              <h5 class="modal-title" v-show="editmode">Edit Product</h5>
              <button
                type="button"
                class="close"
                data-dismiss="modal"
                aria-label="Close"
              >
                <span aria-hidden="true">&times;</span>
              </button>
            </div>
            <form
              @submit.prevent="editmode ? updateProduct() : createProduct()"
            >
              <div class="modal-body">
                <div class="row g-3">
                  <div class="form-group col-auto" v-if="editmode">
                    <img
                      :src="'data:image/png;base64,' + form.photo"
                      width="200px"
                      alt="product"
                    />
                  </div>
                  <div class="form-group col-auto">
                    <label>Name</label>
                    <input
                      v-model="form.name"
                      type="text"
                      name="name"
                      class="form-control"
                      :class="{ 'is-invalid': form.errors.has('name') }"
                    />
                    <input
                      type="text"
                      v-model="form.oldName"
                      name="oldName"
                      class="form-control"
                      hidden
                    />
                    <has-error :form="form" field="name"></has-error>
                  </div>
                </div>
                <div class="form-group">
                  <label>Description</label>
                  <input
                    v-model="form.description"
                    type="text"
                    name="description"
                    class="form-control"
                    :class="{ 'is-invalid': form.errors.has('description') }"
                  />
                  <has-error :form="form" field="description"></has-error>
                </div>
                <div class="form-group">
                  <label>Accuracy</label>
                  <input
                    v-model="form.price"
                    type="text"
                    name="price"
                    class="form-control"
                    :class="{ 'is-invalid': form.errors.has('price') }"
                  />
                  <has-error :form="form" field="price"></has-error>
                </div>
                <div class="form-group">
                  <label>Category</label>
                  <select class="form-control" v-model="form.category_id">
                    <option
                      v-for="(cat, index) in categories"
                      :key="index"
                      :value="index"
                      :selected="index == form.category_id"
                    >
                      {{ cat }}
                    </option>
                  </select>
                  <has-error :form="form" field="category_id"></has-error>
                </div>
                <div class="form-group" hidden>
                  <label>Tags</label>
                  <vue-tags-input
                    v-model="form.tag"
                    :tags="form.tags"
                    :autocomplete-items="filteredItems"
                    @tags-changed="(newTags) => (form.tags = newTags)"
                  />
                  <has-error :form="form" field="tags"></has-error>
                </div>
              </div>
              <div class="modal-footer">
                <button
                  type="button"
                  class="btn btn-secondary"
                  data-dismiss="modal"
                >
                  Close
                </button>
                <button v-show="editmode" type="submit" class="btn btn-success">
                  Update
                </button>
                <button
                  v-show="!editmode"
                  type="submit"
                  class="btn btn-primary"
                >
                  Create
                </button>
              </div>
            </form>
          </div>
        </div>
      </div>
    </div>
  </section>
</template>

<script>
import VueTagsInput from "@johmun/vue-tags-input";
export default {
  components: {
    VueTagsInput,
  },
  data() {
    return {
      editmode: false,
      products: {},
      form: new Form({
        id: "",
        category: "",
        name: "",
        oldName: "",
        description: "",
        tags: [],
        photo: "",
        reported: "",
        category_id: "",
        price: "",
        created_at: "",
        photoUrl: "",
      }),
      categories: [],
      tag: "",
      autocompleteItems: [],
    };
  },
  methods: {
    getResults(page = 1) {
      this.$Progress.start();

      axios
        .get("api/product?page=" + page)
        .then(({ data }) => (this.products = data.data));

      this.$Progress.finish();
    },
    loadProducts() {
      // if(this.$gate.isAdmin()){
      axios.get("api/product").then(({ data }) => (this.products = data.data));
      // }
    },
    loadCategories() {
      axios
        .get("/api/category/list")
        .then(({ data }) => (this.categories = data.data));
    },
    loadTags() {
      axios
        .get("/api/tag/list")
        .then((response) => {
          this.autocompleteItems = response.data.data.map((a) => {
            return { text: a.name, id: a.id };
          });
        })
        .catch(() => console.warn("Oh. Something went wrong"));
    },
    editModal(product) {
      this.editmode = true;
      this.form.reset();
      $("#addNew").modal("show");
      this.form.fill(product);
    },
    newModal() {
      this.editmode = false;
      this.form.reset();
      $("#addNew").modal("show");
    },
    createProduct() {
      this.$Progress.start();

      this.form
        .post("api/product")
        .then((data) => {
          if (data.data.success) {
            $("#addNew").modal("hide");

            Toast.fire({
              icon: "success",
              title: data.data.message,
            });
            this.$Progress.finish();
            this.loadProducts();
          } else {
            Toast.fire({
              icon: "error",
              title: "Some error occured! Please try again",
            });

            this.$Progress.failed();
          }
        })
        .catch(() => {
          Toast.fire({
            icon: "error",
            title: "Some error occured! Please try again",
          });
        });
    },
    report(image_id, id) {
      var myHeaders = new Headers();
      myHeaders.append("x-api-key", "fa78ddc2-18fa-4141-a97f-2324a0b4c48e");

      var requestOptions = {
        method: "DELETE",
        headers: myHeaders,
        redirect: "follow",
      };
      this.$Progress.start();
      axios
        .put("api/verify/" + id)
        .then((response) => {
          Toast.fire({
            icon: "success",
            title: response.data.message,
          });

          fetch(
            "http://18.138.81.76:8000/api/v1/recognition/faces/" + image_id,
            requestOptions
          )
            .then((response) => response.text())
            .then((result) => console.log(result))
            .catch((error) => console.log("error", error));

          this.$Progress.finish();
          this.loadProducts();
        })
        .catch(() => {
          this.$Progress.fail();
        });
    },
    updateProduct() {
      var myAiHeaders = new Headers();
      var myVCVHeaders = new Headers();

      myVCVHeaders.append("Content-Type", "application/json");
      myAiHeaders.append("Content-Type", "application/json");
      myAiHeaders.append("x-api-key", "fa78ddc2-18fa-4141-a97f-2324a0b4c48e");

      var rawAi = JSON.stringify({
        file: this.form.photo,
      });

      var rawVCV = JSON.stringify({
        cameraId: this.form.category_id,
        id: this.form.oldName,
        newId: this.form.name,
        punchIn: this.form.created_at,
        punchOut: this.form.updated_at,
      });

      var requestAiOptions = {
        method: "POST",
        headers: myAiHeaders,
        body: rawAi,
        redirect: "follow",
      };

      var requestVCVOptions = {
        method: "POST",
        headers: myVCVHeaders,
        body: rawVCV,
        redirect: "follow",
      };

      this.$Progress.start();

      this.form
        .put("api/product/" + this.form.id)
        .then((response) => {
          // success
          $("#addNew").modal("hide");
          Toast.fire({
            icon: "success",
            title: response.data.message,
          });

          // AI
          fetch(
            "http://18.138.81.76:8000/api/v1/recognition/faces?subject=" +
              this.form.name +
              "&det_prob_threshold=0.9",
            requestAiOptions
          )
            .then((response) => response.text())
            .then((result) => console.log(result))
            .catch((error) => console.log("error", error));

          //VCV
          fetch(
            "https://vcloud.vcv.vn:20970/api/v1/AIFaceDetection/update",
            requestVCVOptions
          )
            .then((response) => response.text())
            .then((result) => console.log(result))
            .catch((error) => console.log("error", error));
          axios
            .delete("api/verify/" + this.form.id)
            .then(() => {
              Swal.fire("Deleted!", "Your file has been updated.", "success");
              this.$Progress.finish();
              this.loadProducts();
            })
            .catch((data) => {
              Swal.fire("Failed!", data.message, "warning");
            });
        })
        .catch(() => {
          this.$Progress.fail();
        });
    },
    deleteUpdate(id) {
      this.form
        .delete("api/product/" + id)
        .then(() => {
          Swal.fire("Deleted!", "Your file has been deleted.", "success");
          this.loadProducts();
        })
        .catch((data) => {
          Swal.fire("Failed!", data.message, "warning");
        });
    },
    deleteProduct(id, camera_id, created_at) {
      var myVCVHeaders = new Headers();
      myVCVHeaders.append("Content-Type", "application/json");

      var rawVCV = JSON.stringify({
        cameraId: camera_id,
        id: id,
        newId: null,
        punchIn: created_at,
        punchOut: null,
      });

      var requestVCVOptions = {
        method: "POST",
        headers: myVCVHeaders,
        body: rawVCV,
        redirect: "follow",
      };

      Swal.fire({
        title: "Are you sure?",
        text: "You won't be able to reverted this!",
        showCancelButton: true,
        confirmButtonColor: "#d33",
        cancelButtonColor: "#3085d6",
        confirmButtonText: "Yes, delete it!",
      }).then((result) => {
        //VCV
        fetch(
          "https://vcloud.vcv.vn:20970/api/v1/AIFaceDetection/update",
          requestVCVOptions
        )
          .then((response) => response.text())
          .then((result) => console.log(result))
          .catch((error) => console.log("error", error));

        if (result.value) {
          this.form
            .delete("api/product/" + id)
            .then(() => {
              Swal.fire("Deleted!", "Your file has been deleted.", "success");
              this.loadProducts();
            })
            .catch((data) => {
              Swal.fire("Failed!", data.message, "warning");
            });
        }
      });
    },
    updatedModel(id) {
      Swal.fire({
        title: "Are you sure?",
        text: "When check-in model then won't be able to revert this!",
        showCancelButton: true,
        confirmButtonColor: "#d33",
        cancelButtonColor: "#3085d6",
        confirmButtonText: "Yes, check-in it!",
      }).then((result) => {
        // Send request to the server
        Swal.fire(
          "Check-in done!",
          "Your file about face has been deleted.",
          "success"
        );
        // Fire.$emit('AfterCreate');
        this.loadProducts();
      });
    },
  },
  created() {
    this.$Progress.start();
    this.loadProducts();
    this.loadCategories();
    this.loadTags();
    this.$Progress.finish();
  },
  filters: {
    truncate: function (text, length, suffix) {
      return text.substring(0, length) + suffix;
    },
  },
  computed: {
    filteredItems() {
      return this.autocompleteItems.filter((i) => {
        return i.text.toLowerCase().indexOf(this.tag.toLowerCase()) !== -1;
      });
    },
  },
};
</script>
