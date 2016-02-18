/*!
 * PGDB
 * Brandon Harrall
 */

 //Updates the modal in mygames.php
function UpdateModal(pEntryID,pTitle,pProgress,pWanted,pAcquired,pPriority,pRating,pDistro) {
	//Locate elements in modal
	var eleEntryID = document.getElementById("UpdateUserEntry");
	var eleProgress = document.getElementById("InputComplete");
	var eleWanted = document.getElementById("InputWanted");
	var eleAcquired = document.getElementById("InputAcquired");
	var elePriority0 = document.getElementById("optionsPriorityArchive");
	var elePriority1 = document.getElementById("optionsPriorityLow");
	var elePriority2 = document.getElementById("optionsPriorityMed");
	var elePriority3 = document.getElementById("optionsPriorityHigh");
	var eleRating = document.getElementById("InputRating");
	var eleDistro = document.getElementById("InputDistro");
	var eleTitle = document.getElementById("ModalTitle");
	
	//Update their values based on each entry
	eleEntryID.value = pEntryID;
	eleProgress.value = pProgress;
	eleWanted.checked = pWanted;
	eleAcquired.checked = pAcquired;
	if (pPriority == 0) {
		elePriority0.checked = 1;
	} else if (pPriority == 1) {
		elePriority1.checked = 1;
	} else if (pPriority == 2) {
		elePriority2.checked = 1;
	} else if (pPriority == 3) {
		elePriority3.checked = 1;
	}
	eleRating.value = pRating;
	eleDistro.value = pDistro;
	eleTitle.innerHTML = pTitle;
}


function changeImage(a) {
	document.getElementById("gamecover").src=a;
}