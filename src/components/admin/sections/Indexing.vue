<template>
  <div class="admin-section">
    <h2>{{ $options.title }}</h2>

    <template v-if="status">
      <NcNoteCard :type="status.indexed_count > 0 ? 'success' : 'warning'">
        {{ t('memories', '{n} media files have been indexed', { n: status.indexed_count }) }}
      </NcNoteCard>

      <div v-if="status.failure_count > 0">
        <NcNoteCard type="warning">
          {{ t('memories', '{n} media files failed indexing and were skipped.', { n: status.failure_count }) }}
          <a :href="API.FAILURE_LOGS()" target="_blank">{{ t('memories', 'View failure logs.') }}</a>
        </NcNoteCard>

        {{ t('memories', 'Files that failed indexing will not be indexed again unless they change.') }}
        {{ t('memories', 'You can manually retry files that failed indexing.') }}
        <br />
        <code>occ memories:index --retry</code>
      </div>

      <NcNoteCard :type="status.last_index_job_status_type">
        {{ t('memories', 'Automatic Indexing status: {status}', { status: status.last_index_job_status }) }}
      </NcNoteCard>
      <NcNoteCard v-if="status.last_index_job_start" :type="status.last_index_job_duration ? 'success' : 'warning'">
        {{ t('memories', 'Last index job was run {t} seconds ago.', { t: status.last_index_job_start }) }}
        {{
          status.last_index_job_duration
            ? t('memories', 'It took {t} seconds to complete.', { t: status.last_index_job_duration })
            : t('memories', 'It is still running or was interrupted.')
        }}
      </NcNoteCard>
      <NcNoteCard v-if="status.last_index_job_start > 3600" type="error">
        {{
          t(
            'memories',
            'Looks like it has been more than an hour since the last index job was run. Make sure Nextcloud cron is configured correctly.',
          )
        }}
      </NcNoteCard>
      <NcNoteCard type="error" v-if="status.bad_encryption">
        {{
          t(
            'memories',
            'Only server-side encryption (OC_DEFAULT_MODULE) is supported, but another encryption module is enabled.',
          )
        }}
      </NcNoteCard>
    </template>

    <p>
      {{
        t(
          'memories',
          'The EXIF indexes are built and checked in a periodic background task. Be careful when selecting anything other than automatic indexing. For example, setting the indexing to only timeline folders may cause delays before media becomes available to users, since the user configures the timeline only after logging in.',
        )
      }}
      {{ t('memories', 'Folders with a ".nomedia" or a ".nomemories" file are always excluded from indexing.') }}
      <NcCheckboxRadioSwitch
        :checked.sync="config['memories.index.mode']"
        value="1"
        name="idxm_radio"
        type="radio"
        @update:checked="update('memories.index.mode')"
        >{{ t('memories', 'Index all media automatically (recommended)') }}
      </NcCheckboxRadioSwitch>
      <NcCheckboxRadioSwitch
        :checked.sync="config['memories.index.mode']"
        value="2"
        name="idxm_radio"
        type="radio"
        @update:checked="update('memories.index.mode')"
        >{{ t('memories', 'Index per-user timeline folders (not recommended)') }}
      </NcCheckboxRadioSwitch>
      <NcCheckboxRadioSwitch
        :checked.sync="config['memories.index.mode']"
        value="3"
        name="idxm_radio"
        type="radio"
        @update:checked="update('memories.index.mode')"
        >{{ t('memories', 'Index a fixed relative path') }}
      </NcCheckboxRadioSwitch>
      <NcCheckboxRadioSwitch
        :checked.sync="config['memories.index.mode']"
        value="0"
        name="idxm_radio"
        type="radio"
        @update:checked="update('memories.index.mode')"
        >{{ t('memories', 'Disable background indexing') }}
      </NcCheckboxRadioSwitch>

      <NcTextField
        :label="t('memories', 'Indexing path (relative, all users)')"
        :label-visible="true"
        :value="config['memories.index.path']"
        @change="update('memories.index.path', $event.target.value)"
        v-if="config['memories.index.mode'] === '3'"
      />
    </p>

    <div>
      {{ t('memories', 'For advanced usage, perform a run of indexing by running:') }}
      <br />
      <code>occ memories:index</code>
      <br />
      {{ t('memories', 'Run index in parallel with 4 threads:') }}
      <br />
      <code>bash -c 'for i in {1..4}; do (occ memories:index &amp;); done'</code>
      <br />
      {{ t('memories', 'Force re-indexing of all files:') }}
      <br />
      <code>occ memories:index --force</code>
      <br />
      {{ t('memories', 'You can limit indexing by user and/or folder:') }}
      <br />
      <code>occ memories:index --user=admin --folder=/Photos/</code>
      <br />
      {{ t('memories', 'Clear all existing index tables:') }}
      <br />
      <code>occ memories:index --clear</code>
    </div>
    <div>
      <h3>Ignore:</h3>

      <NcCheckboxRadioSwitch
        :checked.sync="config['memories.index.ignore_files_and_folders']"
        @update:checked="update('memories.index.ignore_files_and_folders')"
        type="switch"
      >
        {{ t('memories', `Ignore Files and Folders using Regex`) }}
      </NcCheckboxRadioSwitch>
      <div>
        <small><a href="https://regex101.com/" target="_blank">External Regex Checker/Generator</a></small>
      </div>

      <NcTextField
        :label="t('memories', 'Ignore Regex - Example: /^@/')"
        :label-visible="true"
        :value="config['memories.index.ignore_files_and_folders_regex']"
        @change="update('memories.index.ignore_files_and_folders_regex', $event.target.value)"
        :disabled="!config['memories.index.ignore_files_and_folders']"
      />
    </div>
  </div>
</template>

<script lang="ts">
import { defineComponent } from 'vue';

import { translate as t } from '@services/l10n';
import { API } from '@services/API';

import AdminMixin from '../AdminMixin';

export default defineComponent({
  name: 'Indexing',
  title: t('memories', 'Media Indexing'),
  mixins: [AdminMixin],

  data: () => ({ API }),
});
</script>
