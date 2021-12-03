<template>
  <v-app>
    <v-container fluid fill-height id="login-page">
      <v-layout align-center justify-center>
        <v-flex :style="{ 'max-width': '350px' }">
          <material-auth-card>
            <template v-slot:heading>
              <div class="display-1 text-center">
                {{ 'Admin Portal' }}
              </div>
            </template>
            <v-card-text>
              <div class="text-center mb-4">
                <img :src="$base_url + '/assets/images/x_logo.png'" height="100" />
              </div>

              <transition name="fade" mode="out-in">
                <router-view></router-view>
              </transition>
            </v-card-text>
          </material-auth-card>
        </v-flex>
      </v-layout>
    </v-container>
  </v-app>
</template>

<style scoped>
#login-page {
  background-color: #34495e;
}
</style>