<!-- <template>
  <div class="table-responsive">
    <table class="table table-bordered">
      <thead>
        <tr>
          <th>
            <input type="checkbox" @change="toggleAll($event)" />
          </th>
          <th>Name</th>
          <th>Steps</th>
          <th>Created By</th>
        </tr>
      </thead>
      <tbody>
        <tr v-for="lead in allLeads" :key="lead.id">
          <td>
            <input
              type="checkbox"
              :value="lead.id"
              v-model="selectedIds"
            />
          </td>
          <td>{{ lead.name }}</td>
          <td>{{ lead.steps }}</td>
          <td>{{ lead.createdBy }}</td>
        </tr>
      </tbody>
    </table>
  </div>
</template>
<script setup>
import { ref, watch } from 'vue'

const props = defineProps({
  selectedLeads: Array
})
const emit = defineEmits(['update:selectedLeads'])

const allLeads = ref([
  { id: 1, name: 'test action plan', steps: 2, createdBy: 'SuperVisa4u' },
  { id: 2, name: 'Hitesh Test', steps: 3, createdBy: 'SuperVisa4u' },
  { id: 3, name: 'Buyer action plan-2', steps: 7, createdBy: 'Agent Roof' },
  { id: 4, name: 'New Visitor Lead', steps: 1, createdBy: 'SuperVisa4u' }
])

const selectedIds = ref([])

// Update parent on change
watch(selectedIds, (newVal) => {
  const selected = allLeads.value.filter(item => newVal.includes(item.id))
  emit('update:selectedLeads', selected)
})
</script> -->
