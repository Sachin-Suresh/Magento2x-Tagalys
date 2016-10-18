jQuery(window).load(function(){

	$(function() {
				TAGALYS.init({
				  search: {
				  	link: function(q, qf) {
				  		var params = { "q": q, "qf": qf }
				  		return demo_url + '/search?' + TAGALYS.UTILITIES.params_to_query_string(params);
				  	}
				  }
				});
			});

	TAGALYS.init({
	  api: {
	    //server: 'http://api.tagalys.com'
	    server: 'http://staging-api.tagalys.com/'
	  },
	  search_suggestions: {
	    selectors: {
	      search_field: jQuery('#search')
	    },
	    off_align: {
	    	top: jQuery('#search').offset().top + jQuery('#search').height() + 10,
		left: jQuery('#search').offset().left
	    },
	    suggestions: {
	      products: {
	        vertical_position: 'bottom',
	        horizontal_position: 'right'
	      }
	    },
	    failover: function(query) {
	      console.log('failing over to in-house search suggestions for query "' + query + '"');
	    }
	  },
	  search: {
	    link: function(q, qf) {
	      return '/catalogsearch/result/index/?' + TAGALYS.UTILITIES.params_to_query_string({ q: q, qf: qf });
	    }
	  }
	});
	
});