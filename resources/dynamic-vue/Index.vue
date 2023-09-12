<template>
  <Head :title="$t('Redirection')"/>

  <Layout>
    <div class="px-1 px-lg-4">
      <div class="d-flex mb-3">
        <a target="_blank" :href="route('jasmine.redirection.export')" class="btn btn-sm btn-outline-info">
          <i class="bi bi-download"></i>
          {{ $t('Export') }}
        </a>
        <div class="mx-2"><input type="file" accept=".csv" ref="file" class="d-none" @input="importList"></div>
        <button type="button" class="btn btn-sm btn-outline-success" @click.prevent="$refs.file.click()">
          <i class="bi bi-upload"></i>
          {{ $t('Import') }}
        </button>
      </div>
      <div v-for="r in rows" :key="r.id" class="row py-2 mb-4 bg-light">
        <div v-if="!r.id" class="col-md-12">
          <div class="h4" v-text="$t('New redirection')"/>
        </div>
        <div class="col-md-5">
          <div class="field p-1 pt-2 form-group">
            <label class="form-label fw-semibold" :for="r.id+'_from'" v-text="$t('From (old url)')"/>
            <input type="text" :id="r.id+'_from'" v-model="r.from" class="form-control form-control-sm" dir="ltr">
            <div class="text-muted mb-1" style="font-size: .8rem;">{{ decodeURI(r.from) }}</div>
          </div>
          <div class="d-flex align-items-center mt-2">
            <div class="form-check form-switch">
              <input class="form-check-input" type="checkbox" role="switch" :id="r.id+'_regex'" v-model="r.regex">
              <label class="form-check-label" :for="r.id+'_regex'" v-text="$t('Regular Expression')"/>
            </div>
            <div class="mx-2"></div>
            <div class="form-check form-switch">
              <input class="form-check-input" type="checkbox" role="switch" :id="r.id+'_enabled'" v-model="r.enabled">
              <label class="form-check-label" :for="r.id+'_enabled'" v-text="$t('Enabled')"/>
            </div>
            <div class="d-flex mx-4">
              <button type="button" @click.prevent="appendQuery(r)" v-text="$t('Catch query')"
                      class="btn btn-sm btn-outline-info"/>
              <div class="mx-2"></div>
              <button type="button" @click.prevent="httpAndS(r)" v-text="$t('HTTP/HTTPS')"
                      class="btn btn-sm btn-outline-info"/>
            </div>
          </div>
        </div>
        <div class="col-md-5">
          <div class="field p-1 pt-2 form-group">
            <label class="form-label fw-semibold" :for="r.id+'_to'" v-text="$t('To (new url)')"/>
            <input type="text" :id="r.id+'_to'" v-model="r.to" class="form-control form-control-sm" dir="ltr">
            <div class="text-muted mb-1" style="font-size: .8rem;">{{ decodeURI(r.to) }}</div>
          </div>
        </div>

        <div class="col-md-2">
          <div class="field p-1 pt-2 form-group">
            <label class="form-label fw-semibold" :for="r.id+'_permanent'" v-text="$t('Type')"/>
            <select :id="r.id+'_permanent'" v-model="r.permanent" class="form-select form-select-sm">
              <option :value="false">302 ({{ $t('Temporary') }})</option>
              <option :value="true">301 ({{ $t('Permanent') }})</option>
            </select>
          </div>

          <div class="d-flex justify-content-end mt-2">
            <button type="button" class="btn btn-sm btn-primary" @click="save(r)" v-text="$t('Save')"/>
            <div class="mx-2"></div>
            <button v-if="r.id" type="button" class="btn btn-sm btn-danger" @click="remove(r)" v-text="$t('Delete')"/>
          </div>
        </div>
      </div>
    </div>
  </Layout>
</template>

<script>
export default {
  name: 'RedirectionIndex',
  props: {
    rows: {type: Array, required: true},
  },

  methods: {
    save(r) {
      let vm = this;
      this.$inertia.post('', r, {
        preserveScroll: true,
        onSuccess() {
          if (!r.id) vm.newRow();
        },
      });
    },
    remove(r) {
      Swal.fire({
        title: this.$t('Are you sure?'),
        text: this.$t('Are you sure you want to delete') + ' ' + r.id,
        icon: 'warning',
        showCancelButton: true,
        cancelButtonText: this.$t('Cancel'),
        confirmButtonText: this.$t('Ok'),
      }).then(ask => {
        if (!ask.isConfirmed) return false;
        this.$inertia.delete('?id=' + r.id, {preserveScroll: true});
      });
    },
    importList(evt) {
      let fd = new FormData();
      fd.append('file', evt.target.files[0]);
      this.$inertia.post(this.route('jasmine.redirection.import'), fd, {onSuccess: () => document.location.reload()});
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

    newRow() {
      this.rows.push({
        from: '',
        to: '',
        permanent: true,
        regex: false,
        enabled: true,
      });
    },
  },

  beforeMount() {
    this.newRow();
  },
};
</script>

<style scoped>

</style>