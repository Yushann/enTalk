CreateListUI = (function($, window){
	return {
			show: function(){
				Login.forceLogin(function(){
					Source.getSeries();
					$('#createListModal').modal('show');
				});
		 	},
		 	
           updateSeries: function (){
           		var series = Source.seriesList();
           		$('#seriesSelector').empty();
	           	for(var series_id in series) {
	        		$('#seriesSelector').append(new Option(series[series_id], series_id));	
	        	}

				CreateListUI.selectSeries()
           },

           selectSeries: function (){
	           	var season = Source.script()[$('#seriesSelector').val()];
	           	$('#seasonSelector').empty();
		       	for(var season_id = 1; season_id<=season.season_num; season_id++) {
		    		$('#seasonSelector').append(new Option("第 " + season_id + " 季", season_id));	
		    	}

		    	CreateListUI.selectSeason();
           },

           selectSeason: function () {
           		$('#episodeSelector').empty();
           		var season = Source.script()[$('#seriesSelector').val()];
           		var eposide_num = season.episode_mapping[$('#seasonSelector').val()];
           		for(var eposide_id=1; eposide_id <= eposide_num; eposide_id++) {
		    		$('#episodeSelector').append(new Option("第 " + eposide_id + " 集", eposide_id));	
		    	}
		    	CreateListUI.selectEpisode()
           },

           selectEpisode: function () {
           		var season = Source.script()[$('#seriesSelector').val()];
           		var fileName = 'S' + $('#seasonSelector').val() + 'E' + $('#episodeSelector').val();
           		var eposide = season[fileName];
           		$('#episodeContent').empty();
           		$('#episodeContent').append("最佳人數：" + eposide.recommend_num + "</br>");
           		$('#episodeContent').append("<ul><li>" + Object.keys(eposide.recommend_group).join('</li><li>') + "</li></ul>");
           },

           saveToFirestore: function() {
           		if($('#address').val() == "") {
           			alert('請輸入聚會地點');
           			return;
           		}

           		var season = Source.script()[$('#seriesSelector').val()];
           		var fileName = 'S' + $('#seasonSelector').val() + 'E' + $('#episodeSelector').val();
           		var eposide = season[fileName];	
           		var newDoc = db.collection("lists").doc();
           		newDoc.set({
				    title: $('#seriesSelector :selected').text() + fileName,
				    start_time: new Date($('#datetimepicker').data("DateTimePicker").date()),
				    edit_time: new Date(),
				    mode: "face_to_face",
				    mode_data: $('#address').val(),
				    number: eposide.recommend_num,
				    reference: "sources/" + $('#seriesSelector').val() + "/" + fileName,
				    joined: eposide.recommend_group,
				    host_id: userData.uid,
				    host: userData.displayName,
				    host_email: userData.email
				})
				.then(function() {
					$('#createListModal').modal('hide');
					$('#card_container').empty();
					List.getLists();
					List.saveToMyList(newDoc.id);
				    console.log("Document successfully written!");
				})
				.catch(function(error) {
				    console.error("Error writing document: ", error);
				});
           },

           
       };
}(jQuery, window));


Card = (function($, window){
	var render = function(props) {
	  return function(tok, i) {
	    return (i % 2) ? props[tok] : tok;
	  };
	}

	return {
		createCards: function(items) {

			var card_template = $('script[data-template="card_template"]').text().split(/\$\{(.+?)\}/g);
			var card = items.map(function(item) {
			    return card_template.map(render(item)).join('');
			  });
			$('#card_container').append(card);
			LoginView.hide();
		},

		cardReload: function(item) {
			var card_template = $('script[data-template="card_template"]').text().split(/\$\{(.+?)\}/g);
			var card = card_template.map(render(item)).join('');
			$('#' + item.list_id).replaceWith(card);
		},

		join: function(list_id, text, obj){
			Login.forceLogin(function(){
				if(text == lang.meeting_quit) {
					$(obj).data("loading-text", "<i class='fa fa-circle-o-notch fa-spin'></i> 取消中……");
					$(obj).button('loading');
					List.quitMetting(list_id);
				} else if(text == lang.meeting_dissolve) {
					$(obj).data("loading-text", "<i class='fa fa-circle-o-notch fa-spin'></i> 取消中……");
					$(obj).button('loading');
					List.dissolveMetting(list_id);
				} else {
					$(obj).data("loading-text", "<i class='fa fa-circle-o-notch fa-spin'></i> 抽取角色中");
					$(obj).button('loading');
					List.saveToMyList(list_id);
				}
			});
	 	},
	};
}(jQuery, window));

LoginView = (function($, window){
	return {
		show: function(){
			var loading = $('script[data-template="loadingView"]').text().split(/\$\{(.+?)\}/g);
			$('#card_container').append(loading);
		},
		hide: function () {
			$('#loadingView').remove();
		},
	}
}(jQuery, window));


MemberList = (function($, window){
	return {
		show: function(list_id){
			$('#memberListModal').modal('show');
			$('#member_list_body').html($('#'+list_id+'_member_list').val());
		},
	}
}(jQuery, window));


Tab = (function($, window){
	var prepare = function(obj) {
		$('#card_container').empty();
		$('.nav-tabs li a').removeClass("active");
		$(obj).addClass("active");
	}
	
	return {
		showLists: function(obj) {
			prepare(obj);
			LoginView.show();
			List.getLists();
		},
		showEndLists: function(obj) {
			prepare(obj);
			LoginView.show();
			List.getEndLists();
		},
		showMyLists: function(obj) {
			Login.forceLogin(function(){
				prepare(obj);
				LoginView.show();
				List.getAttendedLists();
			});
		},
		updateHistory: function(obj){
			prepare(obj);
			var loading = $('script[data-template="history"]').text().split(/\$\{(.+?)\}/g);
			$('#card_container').append(loading);
		}
    };
}(jQuery, window));