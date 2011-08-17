
(function() {
	var data = dojo.provide("hhreg.admin.data");

	data.role = {
		SYSTEM_ADMIN: 1,
		USER_ADMIN: 2,
		EVENT_ADMIN: 3,
		EVENT_MANAGER: 4,
		EVENT_REGISTRAR: 5,
		VIEW_EVENT: 6,
		
		userHasRole: function(user, roleIds) { 
			// summary:
			//         check if the given user has at least one of the given roles.
			//
			
			if(!dojo.isArray(roleIds)) {
				roleIds = [roleIds];
			}
			
			var i;
			var role;
			for(i=0; i<user.roles.length; ++i) {
				role = user.roles[i];
				if(dojo.indexOf(roleIds, role.roleId) >= 0) {
					return true;
				}
			}
			
			return false;
		},
		userHasRoleForEvent: function(user, roleIds, eventId) {
			// summary:
			//         check if the given user has at least one of the given roles for the given event.
			//
			
			if(!dojo.isArray(roleIds)) {
				roleIds = [roleIds];
			}
			
			var i;
			var role;
			for(i=0; i<user.roles.length; ++i) {
				role = user.roles[i];
				if(eventId == role.eventId && role.roleId ) {
					return true;
				}
			}
			
			return false;
		}
	};
})();
