(function() {
    //Feather Icons initialize
    feather.replace();

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
