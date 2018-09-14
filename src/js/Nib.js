/**
 * Nib - For applying Quilljs to TEXTAREA elements
 *
 * @author Andrew Leach
 * @author Tyler Uebele
 * @see https://github.com/quilljs/quill
 */

/**
 * Nib - the pointed end of a writing utensil
 *
 * Attach Quilljs editor to a TEXTAREA or DIV
 */
class Nib {
    /**
     * Accept optional overrides for the default toolbar and styles
     */
    constructor(options = {}) {
        this.toolbar = options.toolbar || Nib.getDefaultToolbar();
        this.styles = options.styles || Nib.getDefaultStylesheet();
        this.stylesheet = null;
        if (this.styles && this.styles.length) {
            this.stylesheet = Nib.applyStylesheet(this.styles);
        }
    }

    /**
     * Returns a suitable default toolbar for Quill
     *
     * @returns {string[][]}
     */
    static getDefaultToolbar() {
        return [['bold', 'italic', 'underline', 'strike'], ['link', 'image', 'video', 'html']];
    }

    /**
     * Applies provided styles to the current DOM/CSSOM
     *
     * @param styles
     * @returns {StyleSheet}
     */
    static applyStylesheet(styles) {
        let sheet, element = document.createElement('style');
        document.head.appendChild(element);
        sheet = element.sheet;
        for (let i = 0; i < styles.length; i++) {
            sheet.insertRule(styles[i]);
        }
        return sheet;
    }

    /**
     * Returns an array of CSS Rules suitable for Quill
     *
     * @returns {string[]}
     */
    static getDefaultStylesheet() {
        return [`
.ql-toolbar, .ql-editor {
    background:#fff;
}`,`
.ql-editor {
    min-height:100px;
}`,`
.ql-html:after {
    content: "[source]";
    font-size:.7em;
    padding-top:0;
    display:block;
    margin-top: -2px;
}`,`
.ql-html-editor {
    position:absolute;
    background:#111;
    color:#fff;
    top: 0;
    left: 0;
    bottom: 0;
    width:100%;
    border:0;
    padding:12px;
    box-sizing:border-box;
}`];
    }

    /**
     * Accept a selector and apply Quilljs to each matching DOM element
     *
     * @param selector DOM query to select nodes for
     */
    dipByQuery(selector) {
        document.querySelectorAll(selector).forEach(this.dipQuill, this);
    }

    /**
     * Accept an element and apply Quilljs to it by delegating it to the
     * appropriate function
     *
     * @param element DOM element to apply Quilljs to
     */
    dipQuill(element) {
        switch (element.nodeName) {
            case 'TEXTAREA':
                return this.dipQuillInTextarea(element);
            case 'DIV':
                return this.dipQuillInDiv(element);
            default:
                return false;
        }
    }

    /**
     * Seek a toolbar spec from the specified element,
     * If missing, resort to default
     *
     * @param element DOM element to see toolbar from
     */
    getToolbar(element) {
        return (null != element.dataset.qltoolbar)
               ? JSON.parse(element.dataset.qltoolbar)
               : this.toolbar;
    }

    /**
     * Instantiate a new Quilljs object and attach it to supplied TEXTAREA
     *
     * @param quillDiv DIV element Quilljs works in
     * @param textarea TEXTAREA element Quilljs will feed content to
     * @param toolbar  toolbar to use for current Quilljs instance
     * @return {Quill}
     */
    buildQuill(quillDiv, textarea, toolbar) {
        // Initialize a new instance of Quill.js
        let quill = new Quill('#' + quillDiv.id, {
            modules: {
                toolbar: {
                    container: toolbar,
                    handlers: {
                        // Handler for the view source button aka html
                        'html': function() {
                            // if it's visible hide it otherwise show it
                            if (textarea.style.display === 'block') {
                                textarea.style.display = 'none';
                            } else {
                                // If we are making the html editor visible, copy the contents over from quill
                                textarea.value = this.quill.root.innerHTML;
                                textarea.style.height = this.quill.root.clientHeight + 'px';
                                textarea.style.display = 'block';
                            }
                        },
                    }
                }
            },
            theme: 'snow'
        });

        // On change of value for the textarea copy it back over to Quill
        textarea.addEventListener('keyup', function() {
            quill.root.innerHTML = textarea.value;
        });

        // Find the form our wysiwyg belongs to
        let form = quillDiv.closest('form');
        if (null != form) {
            // Create a submission event listener
            form.addEventListener('submit', event => {
                event.preventDefault();

                // For each wysiwyg in our form copy it over to our textarea
                form.querySelectorAll('.quill-editor').forEach(element => {
                    let textarea = 'textarea[name="'+element.id.replace('quill-container-for-', '')+'"]';
                    document.querySelector(textarea).innerHTML
                        = element.querySelector('.ql-editor').innerHTML;

                    // re-trigger submission
                    form.submit()
                })
            })
        }

        // Hide the newly appended text area which is now our view source and append it to our quill editor
        textarea.style.display = 'none';
        textarea.classList.add('ql-html-editor');
        quill.container.appendChild(textarea);

        return quill;
    }

    /**
     * Accept a DIV element and attach Quilljs to it
     *
     * @param quillDiv DIV element Quilljs works in
     * @returns {boolean}
     */
    dipQuillInDiv(quillDiv) {
        if ('DIV' !== quillDiv.nodeName) {
            return false;
        }

        let textarea = document.createElement('textarea');
        textarea.setAttribute('name', quillDiv.id);
        textarea.className = 'ql-html-editor';

        let toolbar = this.getToolbar(quillDiv);

        // Initialize a new instance of Quill.js and attach our TEXTAREA
        var quill = this.buildQuill(quillDiv, textarea, toolbar);

        // Copy quill editor's contents to the TEXTAREA
        textarea.value = quill.root.innerHTML;

        return true;
    }

    /**
     * Accept a TEXTAREA element and attach Quilljs to it
     *
     * @param textarea
     * @returns {boolean}
     */
    dipQuillInTextarea(textarea) {
        if ('TEXTAREA' !== textarea.nodeName) {
            return false;
        }

        // Create a DIV for Quill, insert it before supplied TEXTAREA
        let quillDiv = document.createElement('div');
        quillDiv.id = 'quill-container-for-' + (textarea.id || textarea.name);
        quillDiv.className = 'quill-editor';
        textarea.parentNode.insertBefore(quillDiv, textarea);

        let toolbar = this.getToolbar(textarea);

        // Initialize a new instance of Quill.js and attach our TEXTAREA
        var quill = this.buildQuill(quillDiv, textarea, toolbar);

        // Copy TEXTAREA's height and contents to the quill editor
        quill.root.style.height = textarea.clientHeight + 'px';
        quill.root.innerHTML = textarea.value;

        return true;
    }
}

// Mozilla polyfill for closest
if (window.Element && !Element.prototype.closest) {
    Element.prototype.closest =
        function(s) {
            var matches = (this.document || this.ownerDocument).querySelectorAll(s),
                i,
                el = this;
            do {
                i = matches.length;
                while (--i >= 0 && matches.item(i) !== el) {};
            } while ((i < 0) && (el = el.parentElement));
            return el;
        };
}
