Source = (function($, window){
	var seriesList = {};
    var script = {}; 

    var getSourcesList = function(){
		db.collection("sources").get().then((series) => {
			series.forEach(function(doc) {
				seriesList[doc.id] = doc.data().title;
				script[doc.id] = doc.data().script;
	        });

	        CreateListUI.updateSeries();
		});
	}

	return {
		seriesList: function() {
			return seriesList;
		},

		script: function() {
			return script;
		},

        getSeries: function(){
            if(!Object.keys(seriesList).length) {
				getSourcesList();
			} else{
				CreateListUI.updateSeries();
			}
        }
    };
}(jQuery, window));


List = (function($, window){
	// var defaultCover = "data:image/svg+xml;charset=UTF-8,%3Csvg%20width%3D%22200%22%20height%3D%22250%22%20xmlns%3D%22http%3A%2F%2Fwww.w3.org%2F2000%2Fsvg%22%20viewBox%3D%220%200%20200%20250%22%20preserveAspectRatio%3D%22none%22%3E%3Cdefs%3E%3Cstyle%20type%3D%22text%2Fcss%22%3E%23holder_16163df2338%20text%20%7B%20fill%3A%23eceeef%3Bfont-weight%3Abold%3Bfont-family%3AArial%2C%20Helvetica%2C%20Open%20Sans%2C%20sans-serif%2C%20monospace%3Bfont-size%3A13pt%20%7D%20%3C%2Fstyle%3E%3C%2Fdefs%3E%3Cg%20id%3D%22holder_16163df2338%22%3E%3Crect%20width%3D%22200%22%20height%3D%22250%22%20fill%3D%22%2355595c%22%3E%3C%2Frect%3E%3Cg%3E%3Ctext%20x%3D%2256.203125%22%20y%3D%22131%22%3EThumbnail%3C%2Ftext%3E%3C%2Fg%3E%3C%2Fg%3E%3C%2Fsvg%3E";
	var defaultCover = "";
	var dataTransfer = function(doc){
		var mylist = (localStorage.mylist == undefined) ? {} : JSON.parse(localStorage.mylist);
		var data = doc.data();
		var tmp = {};
		tmp.title = data.title;
		tmp.mode = '';
		tmp.mode_data = data.mode_data;
		tmp.start_time = moment(data.start_time).format('YYYY-MM-DD HH:mm');
		tmp.cover = defaultCover;
		tmp.list_id = doc.id;
		tmp.host = data.host;

		var total = Object.keys(data.joined).length;
		var joined = total - Object.keys(Utility.joinedVacancy(data.joined)).length;
		tmp.joined_number = joined + ' / ' + total;

		if(data.start_time < new Date()){
			tmp.join_btn_disabled = 'disabled';
			tmp.join_btn_name = lang.meeting_is_end;
			tmp.join_btn_class = 'secondary';
		}else if(mylist[doc.id] != undefined){
			tmp.join_btn_disabled = 'disabled';
			tmp.join_btn_name = lang.meeting_joined;
			tmp.join_btn_class = 'secondary';
			// tmp.join_btn_disabled = (userData.uid == data.host_id) ? 'disabled' : '';
			// tmp.join_btn_name = lang.meeting_quit;
			// tmp.join_btn_class = 'success';

			Object.keys(data.joined).forEach(function(key) {
				if(data.joined[key] == userData.uid) {
					tmp.character = key;
					tmp.display_character = '角色：' + key;
				}
			});
		}else if(total == joined){
			tmp.join_btn_disabled = 'disabled';
			tmp.join_btn_name = lang.meeting_is_full;
			tmp.join_btn_class = 'danger';
		} else {
			tmp.join_btn_disabled = '';
			tmp.join_btn_name = lang.meeting_join;
			tmp.join_btn_class = 'info';
		}

		return tmp;
	}

	return {
		getLists: function() {
			db.collection("lists").where('start_time', '>', new Date()).orderBy('start_time', 'asc').limit(100).get().then((list) => {
				var items = [];
				list.forEach(function(doc) {
					items.push(dataTransfer(doc));
		        });
		        Card.createCards(items);
			});
		},

		getAttendedLists: function(showOnPage = true) {
			if(userData == undefined) {
				return;
			}

			db.collection("mylist").doc(userData.uid).collection("attended").orderBy('join_time', 'desc').limit(100).get().then((list) => {
				list.forEach(function(doc) {
					doc.data().reference.get().then(item => {
						var mylist = localStorage.mylist == undefined ? {} : JSON.parse(localStorage.mylist);
						if(showOnPage) {
							Card.createCards([dataTransfer(item)]);	
						}
						mylist[item.id] = dataTransfer(item);
						localStorage.mylist = JSON.stringify(mylist);
					});
		        });
			});
		},

		getEndLists: function() {
			db.collection("lists").where('start_time', '<', new Date()).orderBy('start_time', 'desc').limit(100).get().then((list) => {
				var items = [];
				list.forEach(function(doc) {
					items.push(dataTransfer(doc));
		        });
		        Card.createCards(items);
			});
		},

		saveToMyList: function(list_id) {
			//先檢查是否參加過
			db.collection("mylist").doc(userData.uid).collection("attended").doc(list_id).get().then((attended) => {
				if(attended.data() != undefined) {
					alert('你已經報名了喔!');
					return
				}

				db.collection("lists").doc(list_id).get().then((doc) => {
					var character = Utility.randomCharacter(doc.data().joined);
					var new_joined = doc.data().joined;
					new_joined[character] = userData.uid;
					var batch = db.batch();
					batch.set(
						db.collection("mylist").doc(userData.uid).collection("attended").doc(list_id),
						{
						    reference: db.collection("lists").doc(list_id),
						    join_time: new Date(),
						    character: character,
						}
					);
					batch.update(db.collection("lists").doc(list_id), {joined: new_joined});
					batch.commit().then(function () {
						db.collection("lists").doc(list_id).get().then((doc) => {
							Card.cardReload(dataTransfer(doc));
							alert('你抽中的角色是 ' + character + '\n相關資料將會寄到你的信箱');

							$.ajax({
							  method: "POST",
							  url: "send_mail.php",
							  dataType: 'json',
							  data: {
							  	data: JSON.stringify(doc.data()),
							  	to: userData.email,
							  	character: character
							  },
							});
						});
					    console.log("saveToMyList successfully written!");
					    var mylist = localStorage.mylist == undefined ? {} : JSON.parse(localStorage.mylist);
						mylist[list_id] = dataTransfer(doc);
						localStorage.mylist = JSON.stringify(mylist);
					});
				});
			});
		},

		quitMetting: function(list_id) {
			db.collection("lists").doc(list_id).get().then((doc) => {
				var batch = db.batch();
				batch.delete(db.collection("mylist").doc(userData.uid).collection("attended").doc(list_id));
				batch.update(db.collection("lists").doc(list_id), {joined : Utility.joinedRemoveMyself(doc.data().joined)});
				batch.commit().then(function () {
					db.collection("lists").doc(list_id).get().then((doc) => {
						Card.cardReload(dataTransfer(doc));
					});
				    var mylist = localStorage.mylist == undefined ? {} : JSON.parse(localStorage.mylist);
				    delete mylist[list_id];
					localStorage.mylist = JSON.stringify(mylist);
				});
			});
		}

    };
}(jQuery, window));

