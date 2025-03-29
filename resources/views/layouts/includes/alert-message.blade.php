<script>
    var NotyTheme = function() {
    
    const _componentNoty = function() {
        if (typeof Noty == 'undefined') {
            console.warn('Warning - noty.min.js is not loaded.');
            return;
        }
        // Override Noty defaults
        Noty.overrideDefaults({
            theme: 'limitless',
            layout: 'topRight',
            type: 'alert',
            // timeout: 3500
            timeout: 1000
        });
    
        // Notification Alert type
        @if(in_array(session()->get('type'),array("error", "warning", "success", "info")))
            new Noty({
                text: '<h6 class="mb-1"><i class="{{ session()->get('icon') }} me-1"></i> {{ session()->get('title') }}</h6><label class="form-label">{{ session()->get('text') }}</label>',
                type: "{{ session()->get('type') }}",
                modal: true
            }).show();
        @endif
    
    };
    
    return {
        init: function() {
            _componentNoty();
        }
    }
    }();
    
    
    // Initialize module
    // ------------------------------
    
    document.addEventListener('DOMContentLoaded', function() {
    NotyTheme.init();
    });
    </script>
    
    