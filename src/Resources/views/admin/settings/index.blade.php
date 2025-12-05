<x-admin::layouts>
    <!-- Title of the page -->
    <x-slot:title>
        Settings
    </x-slot>
    <!-- Page Content -->
    <div class="page-content  shiprocket-wrapper">
        <h1 class="text-2xl font-bold text-gray-800 mb-6">Settings</h1>
        <div class="w-full bg-white rounded-lg shadow-md p-8 mb-8">
            <div id="shiprocket-settings-app" class="max-w-4xl mx-auto py-4">
                <v-shiprocket-settings
                    :username="{{ json_encode(old('api_username', $apiUsername ?? '')) }}"
                    :password="{{ json_encode(old('api_password', $apiPassword ?? '')) }}"
                    :domain="{{ json_encode(old('domain', $domain ?? '')) }}"
                    :license="{{ json_encode(old('license_key', $licenseKey ?? '')) }}"
                    :action="{{ json_encode(route('admin.shiprocket.settings.save')) }}"
                    :csrf-token="{{ json_encode(csrf_token()) }}"
                ></v-shiprocket-settings>
            </div>
        </div>
    </div>
    
    @pushOnce('scripts')
        <!-- Shiprocket Settings Form Component Template -->
        <script type="text/x-template" id="v-shiprocket-settings-template">
            <form method="POST" :action="action" @submit="submitForm" class="space-y-8">
                <input type="hidden" name="_token" :value="csrfToken">
                
                <div class="mb-8">
                    <label for="api_username" class="block mb-2 text-base font-medium text-gray-700">Shiprocket API Username</label>
                    <input 
                        type="text" 
                        name="api_username" 
                        id="api_username" 
                        class="w-full border border-gray-300 rounded-md px-4 py-3 focus:outline-none focus:ring-2 focus:ring-blue-500 transition" 
                        v-model="formData.username" 
                        
                    >
                </div>
                
                <div class="mb-8">
                    <label for="api_password" class="block mb-2 text-base font-medium text-gray-700">Shiprocket API Password</label>
                    <div class="relative">
                        <input 
                            v-bind:type="passwordVisible ? 'text' : 'password'" 
                            name="api_password" 
                            id="api_password" 
                            class="w-full border border-gray-300 rounded-md px-4 py-3 focus:outline-none focus:ring-2 focus:ring-blue-500 transition" 
                            v-model="formData.password" 
                           
                        >
                        <button 
                            type="button" 
                            class="absolute inset-y-0 right-0 flex items-center px-4 text-gray-500 hover:text-gray-700 focus:outline-none transition" 
                            tabindex="-1"
                            @click="togglePasswordVisibility"
                        >
                            <span v-text="passwordVisible ? 'Hide' : 'Show'"></span>
                        </button>
                    </div>
                </div>
                
                <div class="mb-8">
                    <label for="domain" class="block mb-2 text-base font-medium text-gray-700">Domain</label>
                    <input 
                        type="text" 
                        name="domain" 
                        id="domain" 
                        class="w-full border border-gray-300 rounded-md px-4 py-3 focus:outline-none focus:ring-2 focus:ring-blue-500 transition" 
                        v-model="formData.domain" 
                        placeholder="example.com"
                        required
                    >
                    <div class="mt-2 text-sm text-gray-500">
                        Enter your domain without http/https (e.g., example.com, mystore.net)
                    </div>
                </div>
                
                <div class="mb-8">
                    <label for="license_key" class="block mb-2 text-base font-medium text-gray-700">License Key</label>
                    <input 
                        type="text" 
                        name="license_key" 
                        id="license_key" 
                        class="w-full border border-gray-300 rounded-md px-4 py-3 focus:outline-none focus:ring-2 focus:ring-blue-500 transition" 
                        v-model="formData.license" 
                        required
                    >
                    <div class="mt-2">
                     <div style="background-color: #fef9c3; border-left: 4px solid #facc15; color: #854d0e; padding: 0.5rem 1rem; border-radius: 0.25rem; font-size: 0.875rem;">
    <strong>Note:</strong> For regular updates and support, please use only a <b>valid license key</b>.
</div>
                    <div class="mt-2 text-sm text-gray-500">
                        If you don't have a license key, please visit our 
                        <a href="https://myapps.wontonee.com" target="_blank" class="text-blue-600 hover:underline">website</a> to purchase one.   
                    </div>

                    </div>
                </div>
                
                <div class="flex justify-between mt-10 pt-4">
                    <button 
                        type="button" 
                        class="bg-white border border-red-500 text-red-600 hover:bg-red-50 font-semibold px-6 py-3 rounded-md shadow-sm transition"
                        @click="confirmRemoveSettings"
                    >
                        Remove Settings
                    </button>
                    
                    <button 
                        type="submit" 
                        class="bg-blue-600 hover:bg-blue-700 text-white font-semibold px-6 py-3 rounded-md shadow-sm transition"
                    >
                        Save Settings
                    </button>
                </div>
            </form>
        </script>

        <script type="module">
          
            // Shiprocket Settings Form Component
            app.component('v-shiprocket-settings', {
                template: '#v-shiprocket-settings-template',
                
                props: {
                    username: {
                        type: String,
                        default: ''
                    },
                    password: {
                        type: String,
                        default: ''
                    },
                    domain: {
                        type: String,
                        default: ''
                    },
                    license: {
                        type: String,
                        default: ''
                    },
                    action: {
                        type: String,
                        required: true
                    },
                    csrfToken: {
                        type: String,
                        required: true
                    }
                },
                
                data() {
                    return {
                        passwordVisible: false,
                        formData: {
                            username: this.username,
                            password: this.password,
                            domain: this.domain,
                            license: this.license
                        }
                    };
                },
                
                methods: {
                    togglePasswordVisibility() {
                        this.passwordVisible = !this.passwordVisible;
                    },
                    
                    confirmRemoveSettings() {
                        if (confirm('Are you sure you want to remove all Shiprocket settings?')) {
                            // Create a separate form for removing settings to bypass validation
                            const form = document.createElement('form');
                            form.method = 'POST';
                            form.action = this.action;
                            form.style.display = 'none';
                            
                            // Add CSRF token
                            const csrfField = document.createElement('input');
                            csrfField.type = 'hidden';
                            csrfField.name = '_token';
                            csrfField.value = this.csrfToken;
                            form.appendChild(csrfField);
                            
                            // Add empty fields
                            const usernameField = document.createElement('input');
                            usernameField.type = 'hidden';
                            usernameField.name = 'api_username';
                            usernameField.value = '';
                            form.appendChild(usernameField);
                            
                            const passwordField = document.createElement('input');
                            passwordField.type = 'hidden';
                            passwordField.name = 'api_password';
                            passwordField.value = '';
                            form.appendChild(passwordField);
                            
                            const domainField = document.createElement('input');
                            domainField.type = 'hidden';
                            domainField.name = 'domain';
                            domainField.value = '';
                            form.appendChild(domainField);
                            
                            const licenseField = document.createElement('input');
                            licenseField.type = 'hidden';
                            licenseField.name = 'license_key';
                            licenseField.value = '';
                            form.appendChild(licenseField);
                            
                            // Add form to document, submit it, and remove it
                            document.body.appendChild(form);
                            form.submit();
                            document.body.removeChild(form);
                        }
                    },
                    
                    submitForm() {
                        // This is just here to allow form submission to work normally
                        // We could add validation or other processing here if needed
                    }
                }
            });
        </script>
    @endPushOnce
</x-admin::layouts>