<template>
  <Head :title="$t('Redirection')"/>

  <Layout>
    <div>
      <div class="card">
        <div class="card-header d-flex justify-content-between">
          <div class="h4 mb-0" v-text="$t('Redirections')"></div>

          <div>
            <a :href="route('jasmine.redirection.export')" target="_blank" class="btn btn-sm btn-outline-info">
              <i class="fas fa-download"></i>
              {{ $t('Export') }}
            </a>

            <button type="button" @click="importRedirections" class="btn btn-sm btn-outline-success">
              <i class="fas fa-upload"></i>
              {{ $t('Import') }}
            </button>
          </div>
        </div>

        <div class="card-body">
          <div v-for="(r,i) in redirections" :key="r.id" class="redirection-item px-1 pt-3 mb-4">
            <div class="d-flex align-items-center">
              <div class="r-id" v-text="r.id"></div>
              <div class="flex-fill">
                <div class="row">
                  <div class="form-group px-2 col-md-5">
                    <label :for="'from_'+r.id">
                      {{ $t('From (old url)') }}
                    </label>
                    <input type="text" :id="'from_'+r.id" v-model="r.from" class="form-control form-control-sm"
                           dir="ltr">
                  </div>

                  <div class="form-group px-2 col-md-5">
                    <label :for="'to_'+r.id">
                      {{ $t('To (new url)') }}
                    </label>
                    <input type="text" :id="'to_'+r.id" v-model="r.to" class="form-control form-control-sm" dir="ltr">
                  </div>

                  <div class="form-group px-2 col-md-2">
                    <label :for="'permanent_'+r.id">
                      {{ $t('Type') }}
                    </label>
                    <select :id="'permanent_'+r.id" v-model="r.permanent" class="form-select form-select-sm">
                      <option :value="false">302 ({{ $t('Temporary') }})</option>
                      <option :value="true">301 ({{ $t('Permanent') }})</option>
                    </select>
                  </div>
                </div>
                <div class="d-flex align-items-center mt-2">
                  <div class="form-group px-2">
                    <div class="custom-control custom-checkbox">
                      <input type="checkbox" class="custom-control-input" :id="'regex_'+r.id"
                             v-model="r.regex">
                      <label class="custom-control-label" :for="'regex_'+r.id">
                        {{ $t('Regular Expression') }}
                      </label>
                    </div>
                  </div>
                  <div class="form-group px-2">
                    <div class="custom-control custom-checkbox">
                      <input type="checkbox" class="custom-control-input" :id="'enabled_'+r.id"
                             v-model="r.enabled">
                      <label class="custom-control-label" :for="'enabled_'+r.id">
                        {{ $t('Enabled') }}
                      </label>
                    </div>
                  </div>

                  <div class="form-group px-2">
                    <button class="btn btn-sm btn-outline-info" :disabled="r.regex"
                            type="button" @click="appendQuery(r)">
                      {{ $t('Catch query') }}
                    </button>

                    <button class="btn btn-sm btn-outline-info"
                            type="button" @click="httpAndS(r)">
                      {{ $t('HTTP/HTTPS') }}
                    </button>
                  </div>

                  <div class="flex-fill"></div>

                  <div class="form-group px-2">
                    <button class="btn btn-sm btn-primary" type="button" @click="save(r)">
                      {{ $t('Save') }}
                    </button>

                    <button class="btn btn-sm btn-danger" type="button" @click="remove(r.id, i)">
                      {{ $t('Delete') }}
                    </button>
                  </div>
                </div>
              </div>
            </div>
          </div>

          <div class="h4" v-text="$t('New Redirection')"></div>
          <div class="redirection-item px-1 pt-3 mb-4">
            <div class="d-flex align-items-center">
              <div class="r-id">-</div>
              <div class="flex-fill">
                <div class="row">
                  <div class="form-group px-2 col-md-5">
                    <label for="new_from">
                      {{ $t('From (old url)') }}
                    </label>
                    <input type="text" id="new_from" v-model="newRedirection.from"
                           class="form-control form-control-sm"
                           :class="{'is-invalid': errors.from}"
                           dir="ltr">
                    <div v-if="errors.from" class="invalid-feedback" role="alert">
                      <strong
                          v-text="Array.isArray(errors.from) ? errors.from[0] : errors.from"/>
                    </div>
                  </div>

                  <div class="form-group px-2 col-md-5">
                    <label for="new_to">
                      {{ $t('To (new url)') }}
                    </label>
                    <input type="text" id="new_to" v-model="newRedirection.to"
                           class="form-control form-control-sm"
                           :class="{'is-invalid': errors.to}"
                           dir="ltr">
                    <div v-if="errors.to" class="invalid-feedback" role="alert">
                      <strong
                          v-text="Array.isArray(errors.to) ? errors.to[0] : errors.to"/>
                    </div>
                  </div>

                  <div class="form-group px-2 col-md-2">
                    <label for="new_permanent">
                      {{ $t('Type') }}
                    </label>
                    <select id="new_permanent" v-model="newRedirection.permanent" class="form-select form-select-sm">
                      <option :value="false">302 ({{ $t('Temporary') }})</option>
                      <option :value="true">301 ({{ $t('Permanent') }})</option>
                    </select>
                  </div>
                </div>
                <div class="d-flex align-items-center mt-2">
                  <div class="form-group px-2">
                    <div class="custom-control custom-checkbox">
                      <input type="checkbox" class="custom-control-input" id="new_regex"
                             v-model="newRedirection.regex">
                      <label class="custom-control-label" for="new_regex">
                        {{ $t('Regular Expression') }}
                      </label>
                    </div>
                  </div>

                  <div class="form-group px-2">
                    <div class="custom-control custom-checkbox">
                      <input type="checkbox" class="custom-control-input" id="new_enabled"
                             v-model="newRedirection.enabled">
                      <label class="custom-control-label" for="new_enabled">
                        {{ $t('Enabled') }}
                      </label>
                    </div>
                  </div>

                  <div class="form-group px-2">
                    <button class="btn btn-sm btn-outline-info" :disabled="newRedirection.regex"
                            type="button" @click="appendQuery(newRedirection)">
                      {{ $t('Catch query') }}
                    </button>

                    <button class="btn btn-sm btn-outline-info"
                            type="button" @click="httpAndS(newRedirection)">
                      {{ $t('HTTP/HTTPS') }}
                    </button>
                  </div>

                  <div class="flex-fill"></div>

                  <div class="form-group px-2">
                    <button class="btn btn-sm btn-primary" type="button" @click="save(newRedirection)">
                      {{ $t('Save') }}
                    </button>
                  </div>
                </div>
              </div>
            </div>
          </div>
        </div>
      </div>

      <form :action="route('jasmine.redirection.import')" method="post" enctype="multipart/form-data"
            class="d-none" ref="importForm">
        <input type="hidden" name="_token" :value="''">
        <input ref="importFormInput"
               type="file" name="file" accept=".*csv" class="d-none">
      </form>
    </div>
  </Layout>
</template>

<script>
export default {
  name: 'RedirectionsManage',
  data() {
    return {
      errors: {},
      newRedirection: {
        from: '',
        to: '',
        permanent: true,
        regex: false,
        enabled: true,
      },

      redirections: [],
    };
  },

  methods: {
    load() {
      fetch('?json=y').then(r => r.json()).then(r => {
        this.redirections = r;
      });
    },

    save(r) {
      let vm = this;
      this.$inertia.post('', r, {
        onSuccess() {
          vm.errors = {};
        },
        onError(errors) {
          vm.errors = errors;
        },
      });
    },

    remove(id, i) {
      let vm = this;
      window.Swal.fire({
        title: vm.$t('swal.Delete'),
        text: vm.$t('swal.sure', {title: id}),
        icon: 'warning',
        showCancelButton: true,
        cancelButtonText: vm.$t('swal.Cancel'),
        confirmButtonText: vm.$t('swal.Yes'),
      }).then(r => {
        if (r.value) vm.$inertia.delete('?id=' + id, {onSuccess: () => vm.load()});
      });
    },

    appendQuery(r) {
      if (r.regex) {
        return;
      }

      r.regex = true;

      // remove query string if exists;
      r.from = r.from.split('?')[0];

      r.from = `^${r.from}(\\?.*)?$`;
      r.to = `${r.to}$1`;

    },

    httpAndS(r) {
      r.regex = true;

      r.from = r.from.replace(/https?:\/\//, 'https?://');
    },

    importRedirections() {
      let vm = this;
      vm.$refs.importFormInput.click();

      vm.$refs.importFormInput.addEventListener('change', function (evt) {
        setTimeout(function () {
          vm.$refs.importForm.submit();
        }, 250);
      });

    },
  },

  mounted() {
    this.load();
  },
};
</script>

<style scoped>

</style>