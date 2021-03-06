function startSortable(table) {
	$(table).find('tbody').sortable({
		axis: "y",
		containment: "parent",
		cursor: "move",
		delay: 150,
		distance: 5,
		handle: ".draggable",
		helper: function(e, tr) {
			var $originals = tr.children();
			var $helper = tr.clone();
			$helper.children().each(function(index) {
				// Set helper cell sizes to match the original sizes
				$(this).width($originals.eq(index).outerWidth());
			});
			$helper.css({"background-color": "#cccccc"});	// TODO: Add this to site CSS instead
				
			return $helper;
		},
		forcePlaceholderSize: true,
	    start: function (event, ui) {
	        // Build a placeholder cell that spans all the cells in the row
	        var cellCount = 0;
	        $('td, th', ui.helper).each(function () {
	            // For each TD or TH try and get its colspan attribute, and add that or 1 to the total
	            var colspan = 1;
	            var colspanAttr = $(this).attr('colspan');
	            if (colspanAttr > 1) {
	                colspan = colspanAttr;
	            }
	            cellCount += colspan;
	        });
	        // Add the placeholder UI - note that this is the item's content, so TD rather than TR
	        ui.placeholder.html('<td colspan="' + cellCount + '">&nbsp;</td>');
	    },
		items: "> tr",
		revert: true,
		update: function (event, ui) {
			onSortableUpdate(event, ui);
		}
	}).disableSelection();	
}

function onSortableUpdate(event, ui) {	// Runs every time an item in a sortable table is released in a new position
	$('#editForm').find('tbody > tr').each(function(currentTrackIndex) {
		updateSort(currentTrackIndex, this);
	});
}

function updateSort(currentTrackIndex, currentRow) {	// Goes through each row in order and updates its sort index
	var currentTrackOrder = currentTrackIndex + 1;
	$(currentRow).find('td > label.setlist_order').html(currentTrackOrder);
	$(currentRow).find('input[id$="SetlistOrder"]').attr('value', currentTrackOrder);
}

function addTrackRow() {	// Add a new row to the end of the form
	var numberRows = $('#editForm').find('tbody > tr').length;
	var newRowNumber = numberRows;
	var newRowIndex = numberRows - 1;

	var lastRow = $('#editForm').find('tbody > tr').last();
	var newRow = lastRow.prev().clone()
	
	newRow.find('[id^="Track"]').each(function() {
		$(this).attr('name', $(this).attr('name').replace(/\[\d+\]/, "[" + newRowIndex + "]"));
		$(this).attr('id', $(this).attr('id').replace(/Track\d+/, "Track" + newRowIndex));
		$(this).val('');
		if($(this).attr('value')) {
			$(this).removeAttr('value');
		}
	});
	newRow.find('input[name$="[id]"]').remove();
	newRow.find('input[name$="[setlist_order]"]').attr('value', newRowNumber);
	newRow.find('label.setlist_order').html(newRowNumber);
	if(selectedKey = newRow.find('option[selected]')) {
		selectedKey.removeAttr('selected');
	}
	
	lastRow.before(newRow);
}

function removeTrack() {	
	var trackID = $(this).closest('tr').find('input[id$="Id"]');
	var numberTracks = $(this).closest('tbody').find('tr').length - 1;	//Removes the bottom button row. TODO: Turn this into a tfooter
	
	if (numberTracks <= 2) {
		alert("Sorry, you cannot have fewer than 2 tracks in a setlist.");
	}
	else {
		var confirm = window.confirm("Are you sure you want to delete this track? There is no undo.");
	}
	
	if (confirm == true) {
		if (trackID.length == 1) {
		$.ajax({
			type: "POST",
			url: "/setlists/deletetrack/" + trackID.attr('value') + "/" + $(this).closest('table').data('editkey'),	// TODO: This probably belongs in TrackController
			success: removeTrackProcessResult(this) })
			.fail(function(jqXHR, textStatus, errorThrown) {
				if (errorThrown == "Forbidden") {
					alert("Sorry, you cannot have fewer than 2 tracks in a setlist.")
				} else {
				alert("Sorry, something went wrong and the track wasn't deleted: " + errorThrown);	
				}});
		}
		else {
			removeTrackRow(this);
		}
	}
}

var removeTrackProcessResult = function(row) {
	return function(data, status, jqXHR) {
		if (data == 1) {
			removeTrackRow(row);
		}
		else {
			alert("Sorry, something went wrong and the track wasn't deleted");
		}
	}
}

function removeTrackRow(row) {
	$(row).closest('tr').remove();
	onSortableUpdate(null, null);
}

function sortableHelper(e, ui) {
	ui.children().each(function() {
        $(this).width($(this).width());
 //       console.debug($(this).width($(this).width()));
    });
    return ui;
}

function updateKeyPreference() {
	var newKey = $(this).val();
	
	ajaxSaveKeyPreference(newKey);
}

function ajaxSaveKeyPreference(newKey) {
	$.ajax({
		type: "POST",
		url: "/setlists/savekeypreference/" + newKey,	// TODO: This probably belongs in KeyController
		success: refreshKeyDisplay(newKey)
		})
		.fail(function(jqXHR, textStatus, errorThrown) {
			if (errorThrown == "Forbidden") {
				alert("Invalid key preference")
			} else {
				alert("Sorry, something went wrong and your key preference wasn't saved: " + errorThrown);	
			}
		});
}

function refreshKeyDisplay(newKey) {
	$('#editForm').find('[id$="KeyStart"]').each(function() {
		$(this).find('[data-' + newKey + ']').each(function() {
			$(this).text($(this).data(newKey));
		});
	});
}