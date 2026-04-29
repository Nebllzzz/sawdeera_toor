    // Ambil elemen dropdown dan input harga
    const produkDropdown = document.getElementById('produk_id');
    const hargaInput = document.getElementById('harga');

    // Event listener untuk perubahan dropdown
    produkDropdown.onchange = () => {
        hargaInput.value = produkDropdown.selectedOptions[0].dataset.harga || '';
    };

    function Fsubtotal() {
        const hp = document.getElementById('harga').value;
        const jp = document.getElementById('jumlah_produk').value;

        const subtotal = hp * jp;

        document.getElementById('subtotal').value = subtotal;
    }

    function Fkembalian() {
        const tb = document.getElementById('total_bayar').value;
        const th = document.getElementById('total_harga').value;
        
        const kembalian = tb - th;
        
        if(kembalian <= 0){
            document.getElementById('kembalian').value = 0;
        }else
            document.getElementById('kembalian').value = kembalian;
    }
