function Chat () {
	
	// attributes
	
	var url = 'chat.php';
	
	var username;
	var rooms;
	var lastPoll;
	
	// functions
	
	this.init = function () {		
		this.rooms = [];
		
		this.join('test');
	}
	
	this.join = function (room) {
		
		// make sure room is a string
		
		if (typeof room != 'string') {
			throw new Error('room must be a string');
		}
		
		// make sure a username has been set
		
		if (typeof this.username == 'undefined') {
			throw new Error('username undefined');
		}
		
		//  make the request
		
		dojo.xhrPost({
		
		});		
	}
	
	// main
	
	this.init();
}