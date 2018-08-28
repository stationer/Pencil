<!-- Include Quill stylesheet -->
<link href="https://cdn.quilljs.com/1.0.0/quill.snow.css" rel="stylesheet">

<form action="/P_Test/quill" method="post">
    <!-- Create the toolbar container -->
    <div id="toolbar">
        <button class="ql-bold">Bold</button>
        <button class="ql-italic">Italic</button>
    </div>

    <!-- Create the editor container -->
    <div id="editor">
        <p>Hello World!</p>
    </div>

    <button type="submit">Submit</button>
</form>


<!-- Include the Quill library -->
<script src="https://cdnjs.cloudflare.com/ajax/libs/jquery/3.3.1/jquery.min.js"></script>
<script src="https://cdn.quilljs.com/1.0.0/quill.js"></script>

<!-- Initialize Quill editor -->
<script>
    const editor = new Quill('#editor', {
        modules: { toolbar: '#toolbar' },
        theme: 'snow'
    });

    $('form').on('submit', function(e) {
        e.preventDefault();
        e.stopPropagation();
        /*$.ajax({
            type: "POST",
            url: '/P_Test/quill',
            data: {
                quill: editor.getContents()
            },
            dataType: 'json'
        });*/
        console.log(editor.getContents());
        return false;
    })
</script>