(function() {

	//Modal
	var modal_btn = document.querySelectorAll('[data-modal]');
	var close_btn = document.querySelectorAll('[data-dismiss]');

	for (var i = 0; i < modal_btn.length; i++) {
		var thisBtn = modal_btn[i];
		thisBtn.addEventListener("click", function() {
			var modal = document.getElementById(this.dataset.modal);
			modal.style.display = "block";
			modal.classList.add("opened");

		}, false);
	}

	for (var i = 0; i < close_btn.length; i++) {
		var thisBtn = close_btn[i];
		thisBtn.addEventListener("click", function() {
			var modal = document.getElementById(this.dataset.dismiss);
			modal.style.display = "none";
			modal.classList.remove("opened");
		}, false);
	}

	window.onclick = function(event) {
		var modal_open = document.getElementsByClassName('c-modal opened')[0];
		if (event.target == modal_open) {
			modal_open.style.display = "none";
			modal_open.classList.remove("opened");
		}
	};

    //Feather Icons initialize
    feather.replace();

    //Navigation Dropdown
    var coll = document.querySelectorAll(".c-nav-menu > li a");
    var i;

    for (i = 0; i < coll.length; i++) {
        coll[i].addEventListener("click", function(event) {
            this.classList.toggle("active");
            let content = this.nextElementSibling;
            if (content) {
                event.preventDefault();

                if (content.style.maxHeight) {
                    content.style.maxHeight = null;
                } else {
                    content.style.maxHeight = content.scrollHeight + "px";
                    if (this.closest("ul.sub")) {
                        this.closest("ul.sub").style.maxHeight =
                            this.closest("ul.sub").scrollHeight + content.scrollHeight + "px";
                    }

                }
            }
        });
    }
})();
