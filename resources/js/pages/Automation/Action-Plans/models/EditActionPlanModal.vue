<template>
  <div class="modal-backdrop-custom" @click.self="closeModal">
    <div class="modal-content-custom">
      <div class="modal-header-custom">
        <h5 class="modal-title-custom">Edit Action Plan</h5>
        <button type="button" class="close-button-custom" @click="closeModal">
          &times;
        </button>
      </div>
      <div class="modal-body-custom">
        <form @submit.prevent="submitForm" novalidate>
          <!-- Action Plan Name -->
          <div class="form-group mb-3">
            <label for="actionPlanName" class="form-label-custom">* Action plan name</label>
            <input
              id="actionPlanName"
              type="text"
              class="form-control-custom"
              v-model="actionPlanName"
              :class="{ 'input-error': errors.actionPlanName }"
              @input="clearFieldError('actionPlanName')"
              autocomplete="off"
            />
            <div v-if="formAttempted && errors.actionPlanName" class="error-text">
              {{ errors.actionPlanName }}
            </div>
          </div>

          <!-- Pause On Reply -->
          <div class="form-check mb-4">
            <input
              type="checkbox"
              class="form-check-input-custom"
              id="pauseOnReply"
              v-model="pauseOnReply"
            />
            <label class="form-check-label-custom" for="pauseOnReply">
              Pause the action plan when the lead replies to the mail
            </label>
          </div>

          <!-- Actions List -->
          <div
            v-for="(action, index) in actions"
            :key="action.id"
            class="action-item-container mb-4 p-3 border rounded"
          >
            <div class="d-flex justify-content-end">
              <button
                type="button"
                class="btn btn-danger btn-sm"
                @click="removeAction(index)"
                :disabled="actions.length === 1"
                title="Remove Action"
              >
                <i class="fas fa-trash-alt"></i>
              </button>
            </div>

            <!-- Action Type Selection -->
            <div class="form-group row mb-3 align-items-center">
              <label
                :for="'actionSelect-' + action.id"
                class="col-sm-2 col-form-label-custom"
                >* Action</label
              >
              <div class="col-sm-5">
                <select
                  :id="'actionSelect-' + action.id"
                  class="form-select-custom"
                  v-model="action.type"
                  @change="handleActionChange(action)"
                  :class="{ 'input-error': errors[action.id]?.type }"
                  @blur="validateField(action.id, 'type')"
                  required
                >
                  <option value="" disabled>Please select an action plan</option>
                  <option value="Create Task">Create Task</option>
                  <option value="Send Email">Send Email</option>
                  <option value="Send Text">Send Text</option>
                  <option value="Change Stage">Change Stage</option>
                  <option value="Add Note">Add Note</option>
                  <option value="Add Tag(s)">Add Tag(s)</option>
                  <option value="Remove Tag(s)">Remove Tag(s)</option>
                  <option value="Remove all Tags">Remove all Tags</option>
                  <option value="Pause all other Action plans"
                    >Pause all other Action plans</option
                  >
                  <option value="Pause Specific Action plan"
                    >Pause Specific Action plan</option
                  >
                  <option value="Repeat Action Plan">Repeat Action Plan</option>
                  <option value="Assign Action Plan">Assign Action Plan</option>
                </select>
                <div v-if="errors[action.id]?.type" class="error-text">
                  {{ errors[action.id].type }}
                </div>
              </div>

              <div class="col-sm-5 d-flex align-items-center">
                <span class="me-2">Immediately run</span>
                <input
                  type="number"
                  min="0"
                  class="form-control-custom w-auto me-2"
                  v-model.number="action.delayValue" />
                <select
                  class="form-select-custom w-auto me-2"
                  v-model="action.delayUnit"
                >
                  <option value="Days">Days</option>
                  <option value="Hours">Hours</option>
                </select>
                <span>after the previous step</span>
              </div>
            </div>

            <!-- Conditional Fields based on Action Type -->
            <!-- Create Task -->
            <div
              v-if="action.type === 'Create Task'"
              class="conditional-fields p-3 mt-3 border rounded"
            >
              <div class="form-group mb-3">
                <label
                  :for="'taskName-' + action.id"
                  class="form-label-custom"
                  >* Task Name</label
                >
                <input
                  type="text"
                  :id="'taskName-' + action.id"
                  class="form-control-custom"
                  v-model="action.taskName"
                  :class="{ 'input-error': errors[action.id]?.taskName }"
                  @blur="validateField(action.id, 'taskName')"
                  autocomplete="off"
                  required
                />
                <div v-if="formAttempted && errors[action.id]?.taskName" class="error-text">
                  {{ errors[action.id].taskName }}
                </div>
              </div>
              <div class="form-group mb-3">
                <label
                  :for="'taskType-' + action.id"
                  class="form-label-custom"
                  >* Task Type</label
                >
                <select
                  :id="'taskType-' + action.id"
                  class="form-select-custom"
                  v-model="action.taskType"
                  :class="{ 'input-error': errors[action.id]?.taskType }"
                  @blur="validateField(action.id, 'taskType')"
                  required
                >
                  <option value="">Select Type</option>
                  <option value="call">Call</option>
                  <option value="email">Email</option>
                  <option value="text">Text</option>
                  <option value="closing">Closing</option>
                  <option value="showing">Showing</option>
                  <option value="open house">Open House</option>
                  <option value="thank you">Thank You</option>
                </select>
                <div v-if="errors[action.id]?.taskType" class="error-text">
                  {{ errors[action.id].taskType }}
                </div>
              </div>
            </div>

            <!-- Send Email -->
            <div
              v-if="action.type === 'Send Email'"
              class="conditional-fields p-3 mt-3 border rounded"
            >
              <div class="form-group mb-3">
                <label
                  :for="'emailTemplate-' + action.id"
                  class="form-label-custom"
                  >* Email Template</label
                >
                <select
                  :id="'emailTemplate-' + action.id"
                  class="form-select-custom"
                  v-model="action.emailTemplate"
                  :class="{ 'input-error': errors[action.id]?.emailTemplate }"
                  @blur="validateField(action.id, 'emailTemplate')"
                  required
                >
                  <option value="">Select Template</option>
                  <option
                    v-for="template in emailTemplates"
                    :key="template.id"
                    :value="template.id"
                  >
                    {{ template.title }}
                  </option>
                </select>
                <div v-if="errors[action.id]?.emailTemplate" class="error-text">
                  {{ errors[action.id].emailTemplate }}
                </div>
              </div>
            </div>

            <!-- Send Text -->
            <div
              v-if="action.type === 'Send Text'"
              class="conditional-fields p-3 mt-3 border rounded"
            >
              <div class="form-group mb-3">
                <label
                  :for="'textTemplate-' + action.id"
                  class="form-label-custom"
                  >* Text Template</label
                >
                <select
                  :id="'textTemplate-' + action.id"
                  class="form-select-custom"
                  v-model="action.textTemplate"
                  :class="{ 'input-error': errors[action.id]?.textTemplate }"
                  @blur="validateField(action.id, 'textTemplate')"
                  required
                >
                  <option value="">Select Template</option>
                  <option
                    v-for="template in textTemplates"
                    :key="template.id"
                    :value="template.id"
                  >
                    {{ template.name }}
                  </option>
                </select>
                <div v-if="errors[action.id]?.textTemplate" class="error-text">
                  {{ errors[action.id].textTemplate }}
                </div>
              </div>
            </div>

            <!-- Add Note -->
            <div
              v-if="action.type === 'Add Note'"
              class="conditional-fields p-3 mt-3 border rounded"
            >
              <div class="form-group mb-3">
                <label
                  :for="'noteContent-' + action.id"
                  class="form-label-custom"
                  >* Note Content</label
                >
                <textarea
                  :id="'noteContent-' + action.id"
                  class="form-control-custom"
                  v-model="action.noteContent"
                  rows="3"
                  :class="{ 'input-error': errors[action.id]?.noteContent }"
                  @blur="validateField(action.id, 'noteContent')"
                  required
                ></textarea>
                <div v-if="errors[action.id]?.noteContent" class="error-text">
                  {{ errors[action.id].noteContent }}
                </div>
              </div>
            </div>

            <!-- Change Stage -->
            <div
              v-if="action.type === 'Change Stage'"
              class="conditional-fields p-3 mt-3 border rounded"
            >
              <div class="form-group mb-3">
                <label class="form-label-custom">* Assign Stage(s)</label>
                <div
                  v-for="stage in stageOptions"
                  :key="stage.id"
                  class="form-check"
                >
                  <input
                    type="checkbox"
                    :id="'stage-' + stage.id + '-' + action.id"
                    class="form-check-input-custom"
                    :value="stage.id"
                    v-model="action.newStage"
                    @change="validateField(action.id, 'newStage')"
                  />
                  <label
                    :for="'stage-' + stage.id + '-' + action.id"
                    class="form-check-label-custom"
                  >
                    {{ stage.name }}
                  </label>
                </div>
                <div v-if="errors[action.id]?.newStage" class="error-text">
                  {{ errors[action.id].newStage }}
                </div>
              </div>
            </div>

            <!-- Add Tag(s) -->
            <div
              v-if="action.type === 'Add Tag(s)'"
              class="conditional-fields p-3 mt-3 border rounded"
            >
              <div class="form-group mb-3">
                <label class="form-label-custom">* Tags to Add</label>
                <div v-for="tag in tagOptions" :key="tag.id" class="form-check">
                  <input
                    type="checkbox"
                    :id="'addTag-' + tag.id + '-' + action.id"
                    class="form-check-input-custom"
                    :value="tag.id"
                    v-model="action.addTags"
                    @change="validateField(action.id, 'addTags')"
                  />
                  <label
                    :for="'addTag-' + tag.id + '-' + action.id"
                    class="form-check-label-custom"
                  >
                    {{ tag.name }}
                  </label>
                </div>
                <div v-if="errors[action.id]?.addTags" class="error-text">
                  {{ errors[action.id].addTags }}
                </div>
              </div>
            </div>

            <!-- Remove Tag(s) -->
            <div
              v-if="action.type === 'Remove Tag(s)'"
              class="conditional-fields p-3 mt-3 border rounded"
            >
              <div class="form-group mb-3">
                <label class="form-label-custom">* Tags to Remove</label>
                <div v-for="tag in tagOptions" :key="tag.id" class="form-check">
                  <input
                    type="checkbox"
                    :id="'removeTag-' + tag.id + '-' + action.id"
                    class="form-check-input-custom"
                    :value="tag.id"
                    v-model="action.removeTags"
                    @change="validateField(action.id, 'removeTags')"
                  />
                  <label
                    :for="'removeTag-' + tag.id + '-' + action.id"
                    class="form-check-label-custom"
                  >
                    {{ tag.name }}
                  </label>
                </div>
                <div v-if="errors[action.id]?.removeTags" class="error-text">
                  {{ errors[action.id].removeTags }}
                </div>
              </div>
            </div>

            <!-- Pause Specific Action plan -->
            <div
              v-if="action.type === 'Pause Specific Action plan'"
              class="conditional-fields p-3 mt-3 border rounded"
            >
              <div class="form-group mb-3">
                <label
                  :for="'pauseSpecificPlan-' + action.id"
                  class="form-label-custom"
                  >* Select Action Plan to Pause</label
                >
                <select
                  :id="'pauseSpecificPlan-' + action.id"
                  class="form-select-custom"
                  v-model="action.pauseSpecificPlan"
                  :class="{ 'input-error': errors[action.id]?.pauseSpecificPlan }"
                  @blur="validateField(action.id, 'pauseSpecificPlan')"
                  required
                >
                  <option value="">Select Plan</option>
                  <option v-for="plan in allActionPlans" :key="plan.id" :value="plan.id">
                    {{ plan.name }}
                  </option>
                </select>
                <div v-if="errors[action.id]?.pauseSpecificPlan" class="error-text">
                  {{ errors[action.id].pauseSpecificPlan }}
                </div>
              </div>
            </div>

            <!-- Assign Action Plan -->
            <div
              v-if="action.type === 'Assign Action Plan'"
              class="conditional-fields p-3 mt-3 border rounded"
            >
              <div class="form-group mb-3">
                <label
                  :for="'assignActionPlan-' + action.id"
                  class="form-label-custom"
                  >* Assign Action Plan</label
                >
                <select
                  :id="'assignActionPlan-' + action.id"
                  class="form-select-custom"
                  v-model="action.assignActionPlan"
                  :class="{ 'input-error': errors[action.id]?.assignActionPlan }"
                  @blur="validateField(action.id, 'assignActionPlan')"
                  required
                >
                  <option value="">Select Plan</option>
                  <option v-for="plan in allActionPlans" :key="plan.id" :value="plan.id">
                    {{ plan.name }}
                  </option>
                </select>
                <div v-if="errors[action.id]?.assignActionPlan" class="error-text">
                  {{ errors[action.id].assignActionPlan }}
                </div>
              </div>
            </div>
          </div>

          <!-- Add Action Button -->
          <button
            type="button"
            class="btn btn-outline-secondary mt-3 d-flex align-items-center gap-2"
            @click="addAction"
            :disabled="!canAddAction"
          >
            <i class="fas fa-plus"></i> Add Action
          </button>
        </form>
      </div>

      <div class="modal-footer-custom">
        <button type="button" class="btn btn-secondary-custom" @click="closeModal">
          Cancel
        </button>
        <button type="submit" class="btn btn-success-custom" @click="submitForm">
          Update
        </button>
      </div>
    </div>
  </div>
</template>

<script setup>
import axios from 'axios';
import { computed, onMounted, reactive, ref, watch } from 'vue';

const props = defineProps({
  actionPlan: {
    type: Object,
    required: true,
  },
});

const emit = defineEmits(['close', 'submit']);

const actionPlanName = ref('');
const pauseOnReply = ref(false);

const actions = ref([]); 

let actionIdCounter = Date.now(); 

const formAttempted = ref(false);
const errors = reactive({});

const emailTemplates = ref([]);
const textTemplates = ref([]);
const stageOptions = ref([]);
const tagOptions = ref([]);
const allActionPlans = ref([]);

axios.defaults.headers.common['Authorization'] = `Bearer ${localStorage.getItem('auth_token')}`;

const fetchEmailTemplates = async () => {
  try {
    const response = await axios.get('/api/templates');
    emailTemplates.value = response.data.data ?? response.data;
  } catch (error) {
    console.error('Failed to load email templates:', error);
  }
};

const fetchSmsTemplates = async () => {
  try {
    const response = await axios.get('/api/sms-templates');
    textTemplates.value = response.data.templates;
  } catch (error) {
    console.error('Failed to load SMS templates:', error);
  }
};

const fetchStages = async () => {
  try {
    const res = await axios.get('/api/items?type=stage');
    stageOptions.value = res.data.stages || [];
  } catch (err) {
    console.error('Failed to load stages:', err);
  }
};

const fetchTags = async () => {
  try {
    const res = await axios.get('/api/items?type=tag');
    tagOptions.value = res.data.tags || [];
  } catch (err) {
    console.error('Failed to load tags:', err);
  }
};

const fetchAllActionPlans = async () => {
  try {
    const response = await axios.get('/api/automation/action-plans'); 
    allActionPlans.value = response.data.data ?? response.data;
  } catch (error) {
    console.error('Failed to load all action plans for dropdown:', error);
  }
};

const populateModal = () => {
  actionPlanName.value = props.actionPlan.name;
  pauseOnReply.value = props.actionPlan.pause_on_reply;
  
  actions.value = props.actionPlan.actions.map(action => {
    let delayValue = action.delay_days;
    let delayUnit = 'Days'; 

    return {
      id: Date.now() + Math.random(),
      backend_id: action.id,
      type: action.type,
      delayValue: delayValue,
      delayUnit: delayUnit,
      taskName: action.task_name,
      taskType: action.task_type,
      emailTemplate: action.email_template_id,
      textTemplate: action.sms_template_id,
      newStage: action.new_stage || [],
      noteContent: action.note_content,
      addTags: action.add_tags || [],
      removeTags: action.remove_tags || [],
      pauseSpecificPlan: action.pause_specific_plan,
      assignActionPlan: action.assign_action_plan,
    };
  });

  actionIdCounter = actions.value.length > 0 ? Math.max(...actions.value.map((a) => a.id)) : Date.now();
};


function clearFieldError(fieldKey, actionId = null) {
  if (actionId === null) {
    if (errors[fieldKey]) delete errors[fieldKey];
  } else {
    if (errors[actionId]) {
      if (errors[actionId][fieldKey]) {
        delete errors[actionId][fieldKey];
        if (Object.keys(errors[actionId]).length === 0) {
          delete errors[actionId];
        }
      }
    }
  }
}

function validateField(actionId, fieldKey) {
    const action = actions.value.find((a) => a.id === actionId);
    if (!action) return true;

    const fieldValue = action[fieldKey];
    let message = '';

    switch (fieldKey) {
        case 'type':
            if (!fieldValue) message = 'Action Type is required';
            break;

        case 'taskName':
        case 'taskType':
            if (action.type === 'Create Task' && !fieldValue)
                message = fieldKey === 'taskName' ? 'Task Name is required' : 'Task Type is required';
            break;

        case 'emailTemplate':
            if (action.type === 'Send Email' && !fieldValue) message = 'Email Template is required';
            break;

        case 'textTemplate':
            if (action.type === 'Send Text' && !fieldValue) message = 'Text Template is required';
            break;

        case 'newStage':
            break;

        case 'noteContent':
            if (action.type === 'Add Note' && !fieldValue) message = 'Note Content is required';
            break;

        case 'addTags':
            break;

        case 'removeTags':
            break;

        case 'pauseSpecificPlan':
            if (action.type === 'Pause Specific Action plan' && !fieldValue)
                message = 'Please select an Action Plan to pause';
            break;

        case 'assignActionPlan':
            if (action.type === 'Assign Action Plan' && !fieldValue)
                message = 'Please select an Action Plan to assign';
            break;
    }

    if (message) {
        if (!errors[actionId]) errors[actionId] = {};
        errors[actionId][fieldKey] = message;
        return false;
    } else {
        if (errors[actionId]) {
            delete errors[actionId][fieldKey];
            if (Object.keys(errors[actionId]).length === 0) {
                delete errors[actionId];
            }
        }
        return true;
    }
}

function validateAction(action) {
    const fieldsToValidate = ['type'];

    switch (action.type) {
        case 'Create Task':
            fieldsToValidate.push('taskName', 'taskType');
            break;
        case 'Send Email':
            fieldsToValidate.push('emailTemplate');
            break;
        case 'Send Text':
            fieldsToValidate.push('textTemplate');
            break;
        case 'Change Stage':
            break; 
        case 'Add Note':
            fieldsToValidate.push('noteContent');
            break;
        case 'Add Tag(s)':
            break;
        case 'Remove Tag(s)':
            break;
        case 'Pause Specific Action plan':
            fieldsToValidate.push('pauseSpecificPlan');
            break;
        case 'Assign Action Plan':
            fieldsToValidate.push('assignActionPlan');
            break;
    }

    let valid = true;
    for (const field of fieldsToValidate) {
        const fieldValid = validateField(action.id, field);
        if (!fieldValid) valid = false;
    }
    return valid;
}

function isActionEffectivelyEmpty(action) {
    return !action.type &&
           !action.taskName && !action.taskType &&
           !action.emailTemplate && !action.textTemplate &&
           action.newStage.length === 0 && !action.noteContent &&
           action.addTags.length === 0 && action.removeTags.length === 0 &&
           !action.pauseSpecificPlan && !action.assignActionPlan;
}

function validateForm() {
    let valid = true;

    if (!actionPlanName.value.trim()) {
        errors.actionPlanName = 'Action Plan Name is required';
        valid = false;
    } else {
        delete errors.actionPlanName;
    }

    actions.value.forEach((action) => {
        if (!validateAction(action)) {
            valid = false;
        }
    });

    return valid;
}

const addAction = () => {
    if (!canAddAction.value) {
        formAttempted.value = true;
        validateAction(actions.value[actions.value.length - 1]);
        return;
    }

    actionIdCounter++;
    actions.value.push({
        id: actionIdCounter,
        backend_id: null,
        type: '',
        delayValue: 0,
        delayUnit: 'Days',
        taskName: null,
        taskType: null,
        emailTemplate: null,
        textTemplate: null,
        newStage: [],
        noteContent: null,
        addTags: [],
        removeTags: [],
        pauseSpecificPlan: null,
        assignActionPlan: null,
    });
};

const removeAction = (index) => {
    if (actions.value.length > 1) {
        const idToRemove = actions.value[index].id;
        if (errors[idToRemove]) delete errors[idToRemove];
        actions.value.splice(index, 1);
    }
};

const handleActionChange = (action) => {
    action.taskName = null;
    action.taskType = null;
    action.emailTemplate = null;
    action.textTemplate = null;
    action.newStage = [];
    action.noteContent = null;
    action.addTags = [];
    action.removeTags = [];
    action.pauseSpecificPlan = null;
    action.assignActionPlan = null;
    if (errors[action.id]) {
        delete errors[action.id];
    }
};

const canAddAction = computed(() => {
    const planNameValid = actionPlanName.value.trim() !== '';
    if (!actions.value.length) {
        return planNameValid;
    }
    const lastAction = actions.value[actions.value.length - 1];

    if (isActionEffectivelyEmpty(lastAction) && !formAttempted.value) {
        return false;
    }

    return planNameValid && validateAction(lastAction);
});

// Submit form handler for UPDATE
const submitForm = async () => {
  formAttempted.value = true;

  if (!validateForm()) {
    return;
  }

  const formattedActions = actions.value.map((action, index) => {
    let delayInDays = action.delayValue;
    if (action.delayUnit === 'Hours') {
      delayInDays = action.delayValue / 24;
    }

    return {
      ...(action.backend_id && { id: action.backend_id }), 
      type: action.type,
      delay_days: delayInDays,
      task_name: action.taskName,
      task_type: action.taskType,
      email_template_id: action.emailTemplate,
      sms_template_id: action.textTemplate,
      note_content: action.noteContent,
      new_stage: action.newStage,
      add_tags: action.addTags,
      remove_tags: action.removeTags,
      pause_specific_plan: action.pauseSpecificPlan,
      assign_action_plan: action.assignActionPlan,
      step_order: index + 1,
    };
  });

  const formData = {
    id: props.actionPlan.id,
    title: actionPlanName.value,
    pause_on_reply: pauseOnReply.value,
    actions: formattedActions,
  };

  emit('submit', formData);
};

const closeModal = () => {
  emit('close');
};

// On mount, fetch dynamic data and populate modal with existing action plan data
onMounted(() => {
  fetchEmailTemplates();
  fetchSmsTemplates();
  fetchStages();
  fetchTags();
  fetchAllActionPlans();
  populateModal();
});

watch(
  actions,
  () => {
    if (actions.value.length === 0) {
      actionIdCounter = 0; 
    } else {
      actionIdCounter = Math.max(...actions.value.map((a) => a.id), actionIdCounter);
    }
  },
  { deep: true }
);
</script>

<style scoped>
/* Re-use custom modal styles from AddNewModal or define globally */
.modal-backdrop-custom {
  position: fixed;
  z-index: 1050;
  display: flex;
  align-items: center;
  justify-content: center;
  background-color: rgba(0, 0, 0, 50%);
  block-size: 100%;
  inline-size: 100%;
  inset-block-start: 0;
  inset-inline-start: 0;
}

.modal-content-custom {
  position: relative;
  padding: 20px;
  border-radius: 8px;
  background: white;
  box-shadow: 0 4px 12px rgba(0, 0, 0, 15%);
  font-family: Inter, sans-serif;
  inline-size: 90%;
  max-inline-size: 700px;
}

.modal-header-custom {
  display: flex;
  align-items: center;
  justify-content: space-between;
  border-block-end: 1px solid #eee;
  margin-block-end: 20px;
  padding-block-end: 15px;
}

.modal-title-custom {
  color: #333;
  font-size: 1.5rem;
  font-weight: 600;
}

.close-button-custom {
  padding: 0;
  border: none;
  background: none;
  color: #666;
  cursor: pointer;
  font-size: 1.8rem;
  line-height: 1;
}

.modal-body-custom {
  max-block-size: 70vh;
  overflow-y: auto;
  padding-inline-end: 15px;
}

.modal-footer-custom {
  display: flex;
  justify-content: flex-end;
  border-block-start: 1px solid #eee;
  gap: 10px;
  padding-block-start: 15px;
}

.form-group {
  margin-block-end: 1rem;
}

.form-label-custom {
  display: block;
  color: #343a40;
  font-weight: 500;
  margin-block-end: 0.5rem;
}

.form-control-custom,
.form-select-custom {
  display: block;
  border: 1px solid #ced4da;
  border-radius: 0.5rem;
  background-clip: padding-box;
  background-color: #fff;
  color: #495057;
  font-size: 1rem;
  font-weight: 400;
  inline-size: 100%;
  line-height: 1.5;
  padding-block: 0.75rem;
  padding-inline: 1rem;
  transition: border-color 0.15s ease-in-out, box-shadow 0.15s ease-in-out;
}

.form-control-custom:focus,
.form-select-custom:focus {
  border-color: #80bdff;
  box-shadow: 0 0 0 0.2rem rgba(0, 123, 255, 25%);
  outline: 0;
}

.input-error {
  border-color: #dc3545;
}

.error-text {
  color: #dc3545;
  font-size: 0.875em;
  margin-block-start: 0.25rem;
}

.form-check-input-custom {
  border: 1px solid #ced4da;
  border-radius: 0.25em;
  appearance: none;
  background-color: #fff;
  block-size: 1.25em;
  cursor: pointer;
  inline-size: 1.25em;
  margin-block-start: 0.25em;
  vertical-align: top;
}

.form-check-input-custom:checked {
  border-color: #007bff;
  background-color: #007bff;
  background-image: url("data:image/svg+xml,%3csvg xmlns='http://www.w3.org/2000/svg' viewBox='0 0 20 20'%3e%3cpath fill='none' stroke='%23fff' stroke-linecap='round' stroke-linejoin='round' stroke-width='3' d='M6 10l3 3l6-6'/%3e%3c/svg%3e");
  background-position: center;
  background-repeat: no-repeat;
  background-size: 100% 100%;
}

.form-check-label-custom {
  cursor: pointer;
  margin-inline-start: 0.5rem;
}

.action-item-container {
  border: 1px solid #e9ecef;
  background-color: #f8f9fa;
}

.btn-sm {
  border-radius: 0.3rem;
  font-size: 0.875rem;
  padding-block: 0.25rem;
  padding-inline: 0.5rem;
}

.btn-outline-secondary {
  border-color: #6c757d;
  background-color: transparent;
  color: #6c757d;
}

.btn-outline-secondary:hover {
  border-color: #6c757d;
  background-color: #6c757d;
  color: #fff;
}

.btn-success-custom {
  border: none;
  border-radius: 5px;
  background-color: #28a745;
  color: white;
  cursor: pointer;
  padding-block: 8px;
  padding-inline: 15px;
  transition: background-color 0.2s ease;
}

.btn-success-custom:hover {
  background-color: #218838;
}

.btn-secondary-custom {
  border: none;
  border-radius: 5px;
  background-color: #6c757d;
  color: white;
  cursor: pointer;
  padding-block: 8px;
  padding-inline: 15px;
  transition: background-color 0.2s ease;
}

.btn-secondary-custom:hover {
  background-color: #5a6268;
}
</style>
