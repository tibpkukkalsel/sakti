<div class="row mt-2">
    <div class="col-12">
        <table class="table table-bordered">
            <thead>
                <tr>
                    <th>No.</th>
                    <th>Nama Layanan</th>
                    <th>PIC</th>
                    <th>#</th>
                </tr>
            </thead>
            <tbody id="loadsetlistkaryawanlibur"></tbody>
        </table>
    </div>
</div>

<script>
    $(function() {
        function loadsetlistkaryawanlibur() {
            var kode_libur = "{{ $kode_libur }}";
            $("#loadsetlistkaryawanlibur").load('/konfigurasi/harilibur/' + kode_libur +
                '/getsetlistkaryawanlibur');
        }

        loadsetlistkaryawanlibur();
    });
</script>
