<template>
  <v-container
    id="account-view"
    fluid
    tag="section"
  >
    <material-card
      icon="mdi-clipboard-text"
      icon-small
      title="權限管理"
      color="accent"
    >
      <v-data-table
        :headers="headers"
        :items="roles"
        :items-per-page="10"
        height="58vh"
        :fixed-header="true"
        :loading="loading"
        :single-expand="true"
        item-key="name"
        show-expand
      >
        <template v-slot:expanded-item="{headers, item }">
          <td :colspan="headers.length">
            <v-chip
              class="ma-2"
              color="teal"
              text-color="white"
              v-for="menu in item.menus"
              :key="menu.id"
            >
              <v-avatar left>
                <v-icon>mdi-checkbox-marked-circle</v-icon>
              </v-avatar>
              {{ menu.title }}
            </v-chip>
          </td>
        </template>
        <template v-slot:[`item.actions`]="{ item }">
          <v-btn
              color="secondary"
              small
              dark
              class="mx-2"
              @click="editItem(item)"
            >
            <v-icon small>mdi-pencil</v-icon>
          </v-btn>
          <v-btn
              color="error"
              small
              dark
              class="mx-2"
              @click="deleteItem(item)"
            >
            <v-icon small>mdi-delete</v-icon>
          </v-btn>
        </template>
        <template v-slot:no-data>
          <v-btn
            color="primary"
            @click="initialize"
          >
            重新整理
          </v-btn>
        </template>
      </v-data-table>
      <v-dialog v-model="dialog" width="600">
        <v-card>
          <v-card-title class="text-h6 grey lighten-2">
            {{ isCreate ? "新增" : "編輯" }}
          </v-card-title>
          <v-card-subtitle class="mt-3">
            <v-form ref="form">
              <v-container>
                <v-row>
                  <v-col cols="12" md="12">
                    <v-text-field
                      label="角色"
                      prepend-inner-icon="fa fa-user"
                      outlined
                      v-model="form.roleName"
                    ></v-text-field>
                  </v-col>
                </v-row>
                <v-row>
                  <v-col
                    cols="12"
                    md="12"
                    v-for="menu in menus"
                    :key="menu.id"
                  >
                    <v-checkbox
                      v-model="form.menus"
                      :label="menu.title"
                      :value="menu.id"
                      hide-details
                    ></v-checkbox>
                  </v-col>
                </v-row>
              </v-container>
            </v-form>
          </v-card-subtitle>
          <v-divider></v-divider>
          <v-card-actions>
            <v-spacer></v-spacer>
            <v-btn @click="cancel">取消</v-btn>
            <v-btn
              class="btn btn-danger white--text"
              @click="submit"
              >送出</v-btn
            >
          </v-card-actions>
        </v-card>
      </v-dialog>
    </material-card>
  </v-container>
</template>

<script>
import moment from 'moment'

export default {
  name: 'AuthorizationView',

  middleware: 'auth',

  metaInfo () {
    return { title: this.$t('home') }
  },
  data() {
    return {
      headers: [
        { text: "角色", value: "name", width: "200" },
        { text: "權限", value: " ", width: "350", sortable: false },
        { text: " ", value: "id", sortable: false }
      ],
      roles: [],
      loading: false,
      isCreate: false,
      dialog: false,
      form: {
        roleId: null,
        roleName: "",
        menus: []
      },
      menus: []
    };
  },
  mounted() {
    this.getData();
  },
  computed: {
    updatedAt() {
      return time => {
        return moment(time).format("YYYY-MM-DD LT");
      };
    }
  },
  methods: {
     async getData() {
      try {
        this.loading = true;
        const [rolesRes, menuRes] = await Promise.all([
          this.$axios.fetchGet("api/roles_all"),
          this.$axios.fetchGet("api/menu")
        ]);

        this.roles = rolesRes.data.roles;
        this.menus = menuRes.data.menu;
        this.loading = false;
      } catch (err) {
        console.log('Promise.all error message: ', err);
      }
    },
    edit(roleId) {
      let role = this.roles.find(role => role.id === roleId);

      this.form.roleId = role.id;
      this.form.roleName = role.name;
      this.form.menus = role.menus.map(menu => menu.id);
      this.isCreate = false;
      this.dialog = true;
    },
    create() {
      if (this.$refs.form !== undefined) this.$refs.form.reset();

      this.isCreate = true;
      this.dialog = true;
    },
    cancel() {
      this.dialog = false;
    },
    submit() {
      if (this.isCreate) {
        let endpoint = `api/roles`;
        const newRole = {
          name: this.form.roleName,
          menus: this.form.menus
        };

        this.$axios
          .fetchPost(endpoint, newRole)
          .then(({ data }) => {
            this.roles.push(data.role);
            this.$refs.form.reset();
            this.notify("新增角色成功", "success");
          })
          .catch(({ response }) => {
            if (response.status === 422) {
              this.notify(response.data.errors, "error");
            } else {
              Swal.fire({
                text: JSON.stringify(response)
              });
            }
          });
      } else {
        let enpoint = `api/roles/${this.form.roleId}`;
        const role = {
          name: this.form.roleName,
          menus: this.form.menus
        };

        this.$axios
          .fetchPut(enpoint, role)
          .then(({ data }) => {
            const newRoles = this.roles.filter(
              role => role.id !== data.role.id
            );
            newRoles.unshift(data.role);

            this.roles = newRoles;

            this.notify("更新角色成功", "success");
          })
          .catch(({ response }) => {
            if (response.status === 422) {
              this.notify(response.data.errors, "error");
            } else {
              Swal.fire({
                text: JSON.stringify(response)
              });
            }
          });
      }
    },
    notify(data, type = "success") {
      if (type === "success") {
        Swal.fire({
          title: "成功!",
          html: data,
          type: "success",
          confirmButtonClass: "btn btn-success",
          buttonsStyling: false
        });
      } else {
        let errorText = "";

        for (let i in data) {
          errorText = errorText.concat(data[i][0], "<br />");
        }

        Swal.fire({
          title: "錯誤!",
          html: errorText,
          type: "error",
          confirmButtonClass: "btn btn-danger",
          buttonsStyling: false
        });
      }
    }
  }
};
</script>
