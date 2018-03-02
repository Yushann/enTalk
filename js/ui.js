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
		       	for(var season_id in season) {
		    		$('#seasonSelector').append(new Option("第 " + season_id + " 季", season_id));	
		    	}

		    	CreateListUI.selectSeason();
           },

           selectSeason: function () {
           		$('#episodeSelector').empty();
           		var eposides = Source.script()[$('#seriesSelector').val()][$('#seasonSelector').val()];
           		for(var eposide_id in eposides) {
		    		$('#episodeSelector').append(new Option("第 " + eposide_id + " 集", eposide_id));	
		    	}
		    	CreateListUI.selectEpisode()
           },

           selectEpisode: function () {
           		var eposide = Source.script()[$('#seriesSelector').val()][$('#seasonSelector').val()][$('#episodeSelector').val()];
           		$('#episodeContent').empty();
           		$('#episodeContent').append("最佳人數：" + eposide.recommend_num + "</br>");
           		$('#episodeContent').append("<ul><li>" + Object.keys(eposide.recommend_group).join('</li><li>') + "</li></ul>");
           },

           saveToFirestore: function() {
           		if($('#address').val() == "") {
           			alert('請輸入聚會地點');
           			return;
           		}

           		var eposide = Source.script()[$('#seriesSelector').val()][$('#seasonSelector').val()][$('#episodeSelector').val()];
           		var newDoc = db.collection("lists").doc();
           		newDoc.set({
				    title: $('#seriesSelector :selected').text() + 'S' + $('#seasonSelector').val() + 'E' + $('#episodeSelector').val(),
				    start_time: new Date($('#datetimepicker').data("DateTimePicker").date()),
				    edit_time: new Date(),
				    mode: "face_to_face",
				    mode_data: $('#address').val(),
				    number: eposide.recommend_num,
				    reference: "sources/" + $('#seriesSelector').val() + "/" + $('#seasonSelector').val() + "/" + $('#episodeSelector').val(),
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
		},

		cardReload: function(item) {
			var card_template = $('script[data-template="card_template"]').text().split(/\$\{(.+?)\}/g);
			var card = card_template.map(render(item)).join('');
			$('#' + item.list_id).replaceWith(card);
		},

		join: function(list_id, text){
			Login.forceLogin(function(){
				if(text == lang.meeting_quit) {
					List.quitMetting(list_id);
				} else {
					List.saveToMyList(list_id);
				}
			});
	 	},
	};
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
			List.getLists();
		},
		showEndLists: function(obj) {
			prepare(obj);
			List.getEndLists();
		},
		showMyLists: function(obj) {
			Login.forceLogin(function(){
				prepare(obj);
				List.getAttendedLists();
			});
		},
    };
}(jQuery, window));