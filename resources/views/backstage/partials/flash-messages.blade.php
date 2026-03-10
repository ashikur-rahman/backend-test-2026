<script>

    @if( Session::has('success') )
        swal({
            title: "Great!",
            text: "{{ Session::get('success') }}",
            timer: 1500,
            button: false,
            icon: 'success'
        });
    @endif

</script>
