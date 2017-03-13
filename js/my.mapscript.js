/*! TF, 20160410: code to show/hide map on icon click */
(function() { 
  var session_storage = window.sessionStorage || {}; 
  var mapswitcher, 
  map=jQuery("#map"),
  theImage=jQuery("#theImage"); 
  
  function hideMap(delay) { 
    map.hide(delay); 
    mapswitcher.addClass("maphidden").removeClass("mapshown"); 
    theImage.addClass("maphidden").removeClass("mapshown"); 
    session_storage['imageMap'] = 'hidden';
  } 
  
  function showMap(delay) { 
    map.show(delay); 
    mapswitcher.addClass("mapshown").removeClass("maphidden"); 
    theImage.addClass("mapshown").removeClass("maphidden"); 
    session_storage['imageMap'] = 'visible';
  } 
  
  jQuery(function(){ 
    // map show/hide 
    mapswitcher=jQuery("#mapSwitcher"); 
    if (session_storage['imageMap'] == undefined) { 
      session_storage['imageMap'] = 'hidden';
    } 
    if (session_storage['imageMap'] == 'hidden') { 
      hideMap(0); 
    } 
    else { 
      showMap(0); 
    } 
	
    mapswitcher.click(function(e){ 
      if (map.is(":hidden")) { 
        showMap(0); 
      } 
      else { 
        hideMap(0); 
      } 
      e.preventDefault(); 
    }); 
  }); 
}());
