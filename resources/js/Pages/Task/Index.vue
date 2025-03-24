<template>
    <web-layout>
        <!-- Delete Confirmation Modal -->
        <confirmation-modal :show="confirmDeleteActive">
            <template v-slot:title> Confirmation </template>
            <template v-slot:content>
                <p class="p-4">Are you sure you want to delete task(s)?</p>
            </template>
            <template v-slot:footer>
                <o-button variant="danger" @click="destroy">Delete</o-button>
                <div class="mr-3"></div>
                <o-button @click="confirmDeleteActive = false">Cancel</o-button>
            </template>
        </confirmation-modal>

        <div class="card">
            <div class="mx-2 card-body">
                <h3>Task Management</h3>
                <!-- Project Selection or Creation -->
                <div v-if="createProjectMode" class="flex items-center gap-2 mt-4">
                    <label for="project" class="whitespace-nowrap">Select Project:</label>
                    <select v-model="selectedProject" @change="fetchTasks" id="project" class="form-control">
                        <option value="all">All Projects</option>
                        <option v-for="project in projects" :key="project.id" :value="project.id">
                            {{ project.project_name }}
                        </option>
                    </select>
                    <jet-button @click="createProjectMode = true" class="flex items-center gap-1 py-2">
                        <svg xmlns="http://www.w3.org/2000/svg" class="w-5 h-5" fill="none" viewBox="0 0 24 24"
                            stroke="currentColor" stroke-width="2">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M12 4v16m-8-8h16" />
                        </svg>
                        New Project
                    </jet-button>
                </div>

                <!-- Show Create Project Form if no projects exist -->
                <div v-else class="mt-4">
                    <h4>Create a Project</h4>
                    <form @submit.prevent="createProject">
                        <jet-input :class="{ 'text-red-400 bg-red-200': errors.project_name }"
                            v-model="newProject.project_name" placeholder="Project Name" class="mr-2" />
                        <jet-button type="submit" class="mt-2">Create Project</jet-button>
                    </form>
                </div>


                <!-- Create Task Form -->
                <form @submit.prevent="createTask" class="flex gap-2 mt-2">
                    <jet-input :class="{ 'text-red-400 bg-red-200': errors.name }" placeholder="Create To Do"
                        v-model="form.name" />
                    <jet-input :class="{ 'text-red-400 bg-red-200': errors.priority }" placeholder="Priority"
                        v-model="form.priority" />
                    <jet-button>Create</jet-button>
                </form>

                <jet-input-error v-if="errors.name" :message="errors.name" />

                <!-- Task List (Draggable) -->
                <ul id="list-to-do">
                    <draggable v-model="tasks" item-key="id" @end="order">
                        <template #item="{ element }">
                            <li :data-id="element.id" class="flex items-center justify-between px-4 py-3 mt-2 border">
                                <div class="flex items-center gap-2">
                                    <!-- Task Status Toggle -->
                                    <svg xmlns="http://www.w3.org/2000/svg" class="inline w-4 h-4 mr-1" fill="none"
                                        viewBox="0 0 24 24" :stroke="element.status == '1' ? '#0F0' : '#000'"
                                        stroke-width="1" @click="status(element)">
                                        <path v-if="element.status == '1'" stroke-linecap="round"
                                            stroke-linejoin="round" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" />


                                        <path v-else fill-rule="evenodd"
                                            d="M13.477 14.89A6 6 0 015.11 6.524l8.367 8.368zm1.414-1.414L6.524 5.11a6 6 0 018.367 8.367zM18 10a8 8 0 11-16 0 8 8 0 0116 0z"
                                            clip-rule="evenodd" />
                                    </svg>


                                    <!-- Task Name and Priority -->
                                    <span v-show="!element.editMode" @click="element.editMode = true">
                                        Name: {{ element.name }}  Priority: {{ element.priority }}  Created: {{ element.formatted_created_at }} Updated: {{element.formatted_updated_at}}
                                    </span>

                                    <!-- Edit Mode -->
                                    <div v-show="element.editMode">
                                        <jet-input v-model="element.name" placeholder="Task Name" class="mb-1 mr-2"
                                            @keyup.enter="update(element)" />
                                        <jet-input v-model="element.priority" placeholder="Priority"
                                            @keyup.enter="update(element)" />
                                    </div>
                                </div>

                                <!-- Delete Task Button -->
                                <button @click="confirmDelete(element.id)" class="float-right">
                                    <svg xmlns="http://www.w3.org/2000/svg" class="w-6 h-6" fill="#F00"
                                        viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                                        <path stroke-linecap="round" stroke-linejoin="round"
                                            d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" />
                                    </svg>
                                </button>
                            </li>
                        </template>
                    </draggable>
                </ul>

                <!-- Delete All Button -->
                <!-- <div class="flex flex-row-reverse">
                    <jet-button class="mt-2" @click="confirmDeleteActive = true">
                        <svg xmlns="http://www.w3.org/2000/svg" class="w-6 h-6" fill="#F00" viewBox="0 0 24 24"
                            stroke="currentColor" stroke-width="2">
                            <path stroke-linecap="round" stroke-linejoin="round"
                                d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" />
                        </svg>
                        Delete All
                    </jet-button>
                </div> -->

            </div>
        </div>
    </web-layout>
</template>
<script>
import draggable from "vuedraggable";
import WebLayout from "@/Layouts/WebLayout.vue";
import JetInput from "@/Components/TextInput.vue";
import JetButton from "@/Components/PrimaryButton.vue";
import ConfirmationModal from "@/Components/ConfirmationModal.vue";
import { router } from "@inertiajs/vue3";

export default {
    components: {
        WebLayout,
        JetButton,
        JetInput,
        ConfirmationModal,
        draggable,
    },
    props: {
        prop_tasks: Array,
        errors: Object,
        prop_projects: Array,
        selected_project_id: Number,
    },
    data() {
        return {
            tasks: [...this.prop_tasks],
            selectedProject: this.selected_project_id ?? "all",
            projects: [...this.prop_projects],
            form: {
                name: '',
                priority: '',
                project_id: this.selected_project_id,
            },
            newProject: {
                project_name: '',
            },
            confirmDeleteActive: false,
            deleteTaskRow: null,
            createProjectMode: this.prop_projects.length > 0,
        };
    },
    methods: {
        order() {
            const newOrder = this.tasks.map(task => task.id);
            const pId = this.selectedProject;

            router.post(route("tasks.order"), {
                new_order: newOrder,
                project_id: this.selectedProject == "all" ? null : this.selectedProject
            }, {
                preserveScroll: true,
                onSuccess: () => {
                    this.tasks = [...this.prop_tasks];
                },
            });
        },
        fetchTasks() {
            router.get(route("tasks.index"), { 
                project_id: this.selectedProject == "all" ? null : this.selectedProject
            }, {
                preserveScroll: true
            });
        },
        createTask() {
            this.form.project_id = this.selectedProject == "all" ? null : this.selectedProject;
            router.post(route("tasks.store"), this.form, {
                preserveScroll: true,
                onSuccess: () => {
                    this.tasks = [...this.prop_tasks];
                    this.form.name = "";
                    this.form.priority = "";

                },
            });
        },
        createProject() {
            router.post(route("projects.store"), this.newProject, {
                preserveScroll: true,
                onSuccess: () => {
                    this.projects = [...this.prop_projects],
                        this.form.reset();
                    this.createProjectMode = false;
                    this.selectedProject = this.selected_project_id;
                },
            });

        },
        update(task) {
            task.editMode = false;
            router.put(route("tasks.update", task.id), {
                name: task.name,
                priority: task.priority,
            }, {
                preserveScroll: true,
                onSuccess: () => {
                    this.tasks = [...this.prop_tasks];
                },
            });
        },
        status(task) {
            task.status = task.status === "1" ? "0" : "1";
        },
        confirmDelete(id) {
            this.deleteTaskRow = id;
            this.confirmDeleteActive = true;
        },
        destroy() {

            router.delete(route("tasks.destroy", this.deleteTaskRow), {
                preserveScroll: true,
                onSuccess: () => {
                    this.tasks = this.tasks.filter(task => task.id !== this.deleteTaskRow);
                    this.confirmDeleteActive = false;
                    this.deleteTaskRow = null;

                    if (this.tasks.length > 0) {
                        this.order();
                    }
                },
            });
        }
    },
};
</script>
