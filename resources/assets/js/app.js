import vSelect from 'vue-select';

var app = new Vue({
    el: 'body',
    components: {vSelect},
    data: {
        projects: projects,
        selectedProject: null,
        allSprints: sprints,
        selectedSprint: null,
        parentTask: null,
    },
    methods: {
        
        /**
         * Clears the selected sprint if it does not belong to the newly
         * selected project when the selected project is updated.
         **/
        selectedProjectWasUpdated: function() {
            // If sprint is in project, life is peachy so we can return early
            if (this.sprintIsInProject(this.selectedSprint, this.selectedProject)) {
                return;
            }

            // Set selected sprint to null
            this.selectedSprint = null;
        },
        
        /**
         * Updates the selected project to the project which the selected
         * sprint belongs to when the selected sprint is updated.
         **/
        selectedSprintWasUpdated: function() {
            
            // If selected sprint was updated to null, take the rest of the day off
            if (this.selectedSprint == null) {
                return;
            }
            
            // Set the selected project id to the sprint's project
            var selectedSprint = this.selectedSprint;
            this.selectedProject = this.projects.find( function(project) {
                return project.sprints.find( function(sprint) {
                    return sprint.id == selectedSprint.id;
                }) != null;
            });
        },

        /**
         * Returns true if sprint belongs to given project
         *
         * @return bool
         **/
        sprintIsInProject: function(sprint, project) {
            // If sprint is null, return false
            if (sprint == null) {
                return false;
            }
            
            return sprint.project_id == project.id;
        },
    },
    computed: {
        /**
         * Returns the sprints which are available for selection
         **/
        sprints: function() {
            // If no project selected return all sprints
            if (this.selectedProject == null) {
                return $.map(this.projects, function(project) {
                    return project.sprints
                });
            }

            // Return project's sprints
            var selectedProject = this.selectedProject;
            return this.projects.find( function(project) { 
                return project.id == selectedProject.id;
            }).sprints;
        }
    },
    watch: {
        'selectedSprint': 'selectedSprintWasUpdated',
        'selectedProject': 'selectedProjectWasUpdated',
    },
});
