document.addEventListener("DOMContentLoaded", () => {
    const formLogin = document.querySelector("#formLogin");
    if (formLogin) {
        formLogin.addEventListener("submit", (event) => {
            const email = document.querySelector("#email").value.trim();
            const password = document.querySelector("#password").value;
            if (!email.includes("@") || password.length === 0) {
                event.preventDefault();
                alert("Masukkan email dan password yang valid.");
            }
        });
    }

    const formPeminjaman = document.querySelector("#formPeminjaman");
    if (formPeminjaman) {
        formPeminjaman.addEventListener("submit", (event) => {
            const tglPinjam = document.querySelector("#tgl_rencana_pinjam").value;
            const tglKembali = document.querySelector("#tgl_rencana_kembali").value;
            if (tglPinjam && tglKembali && tglKembali < tglPinjam) {
                event.preventDefault();
                alert("Tanggal kembali tidak boleh lebih awal dari tanggal pinjam.");
            }
        });
    }
});

async function fetchBarangJson(keyword = "", kategori = "") {
    const basePath = window.location.pathname.split("/").slice(0, 2).join("/");
    const url = `${basePath}/api/barang.php?search=${encodeURIComponent(keyword)}&kategori=${encodeURIComponent(kategori)}`;
    const response = await fetch(url);
    if (!response.ok) {
        throw new Error(`HTTP ${response.status}`);
    }
    return await response.json();
}