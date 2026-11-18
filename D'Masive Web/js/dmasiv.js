/* D'MASIV – frontend yang membaca konten dari database (lewat dmasiv-admin/api/*.php).
   Dipakai semua halaman. Halaman dikenali dari <body data-page="...">. */
(function () {
    'use strict';

    var API = ((window.DMASIV_CONFIG && window.DMASIV_CONFIG.API_BASE) || '/dmasiv-admin/api').replace(/\/+$/, '');

    /* ───────── Utilitas ───────── */
    function esc(s) {
        return String(s == null ? '' : s).replace(/[&<>"']/g, function (c) {
            return { '&': '&amp;', '<': '&lt;', '>': '&gt;', '"': '&quot;', "'": '&#39;' }[c];
        });
    }
    function href(u) { return /^https?:\/\//i.test(u || '') ? esc(u) : '#'; }
    function iconClass(c) { return /^[\w\- ]{1,60}$/.test(c || '') ? c : ''; }
    function multiline(s) { return esc(s).replace(/\r?\n/g, '<br>'); }
    function $(sel) { return document.querySelector(sel); }
    function put(sel, html) { var el = $(sel); if (el) el.innerHTML = html; return el; }

    var MON_EN = ['Jan', 'Feb', 'Mar', 'Apr', 'May', 'Jun', 'Jul', 'Aug', 'Sep', 'Oct', 'Nov', 'Dec'];
    var MON_ID = ['Januari', 'Februari', 'Maret', 'April', 'Mei', 'Juni', 'Juli', 'Agustus', 'September', 'Oktober', 'November', 'Desember'];
    var DAY_EN = ['Sun', 'Mon', 'Tue', 'Wed', 'Thu', 'Fri', 'Sat'];
    function parts(d) { var m = /^(\d{4})-(\d{2})-(\d{2})/.exec(d || ''); return m ? { y: +m[1], m: +m[2], d: +m[3] } : null; }
    function pad(n) { return (n < 10 ? '0' : '') + n; }
    function tglDM(d) { var p = parts(d); return p ? pad(p.d) + ' ' + MON_EN[p.m - 1] : '-'; }            // 09 Mar
    function tglDMY(d) { var p = parts(d); return p ? pad(p.d) + ' ' + MON_EN[p.m - 1] + ' ' + p.y : '-'; } // 21 Sep 2018
    function tglBulanTahun(d) { var p = parts(d); return p ? MON_ID[p.m - 1] + ' ' + p.y : '-'; }          // Agustus 2018
    function tglHari(d) {                                                                                  // Fri, 21 Sep 2018
        var p = parts(d); if (!p) return '-';
        return DAY_EN[new Date(Date.UTC(p.y, p.m - 1, p.d)).getUTCDay()] + ', ' + tglDMY(d);
    }
    function jam(t) { var m = /^(\d{2}):(\d{2})/.exec(t || ''); return m ? m[1] + ':' + m[2] : null; }
    function rupiahTitik(n) { return 'Rp ' + Number(n).toLocaleString('id-ID'); }        // Rp 119.000
    function rupiahKoma(n) { return 'RP. ' + Number(n).toLocaleString('en-US'); }        // RP. 119,000

    function ambil(path) {
        return fetch(API + '/' + path, { headers: { 'Accept': 'application/json' } })
            .then(function (r) {
                return r.json().then(function (d) {
                    if (!r.ok || !d || !d.ok) throw new Error((d && d.error) || 'Gagal memuat data.');
                    return d;
                }, function () { throw new Error('Respons server tidak valid. Periksa alamat API di js/config.js.'); });
            });
    }
    function keadaan(sel, pesan, galat) {
        var el = $(sel);
        if (el) el.innerHTML = '<p class="dm-state' + (galat ? ' dm-error' : '') + '">' + esc(pesan) + '</p>';
    }
    function muat(sel, path, render, kosong) {
        keadaan(sel, 'Memuat…');
        return ambil(path).then(function (d) {
            var html = render(d);
            if (html === '' || html == null) keadaan(sel, kosong || 'Belum ada data.');
            else put(sel, html);
        }).catch(function (e) { keadaan(sel, e.message, true); });
    }

    /* ───────── Topbar + footer (semua halaman) ───────── */
    function renderSitus(d) {
        var bar = $('.topbar');
        if (bar) {
            bar.querySelectorAll('a').forEach(function (a) { a.remove(); });
            d.media_sosial.forEach(function (s) {
                if (!s.url) return;
                var a = document.createElement('a');
                a.href = s.url; a.target = '_blank'; a.rel = 'noopener noreferrer'; a.setAttribute('aria-label', s.platform);
                a.innerHTML = '<i class="' + iconClass(s.ikon) + '"></i>';
                bar.appendChild(a);
            });
        }
        put('#footer-kontak', '<strong>Contact Us</strong><br />' + d.kontak.map(function (k) {
            return esc(k.nama) + ': <a href="tel:' + esc(k.telepon.replace(/[^\d+]/g, '')) + '">' + esc(k.telepon) + '</a>';
        }).join('<br />'));
        put('#footer-sosmed', d.media_sosial.map(function (s) {
            return '<li><a href="' + href(s.url) + '" target="_blank" rel="noopener noreferrer"><i class="' + iconClass(s.ikon) + '"></i> &nbsp;' + esc(s.platform) + '</a></li>';
        }).join(''));
        put('#footer-mitra', d.mitra.map(function (m) {
            return '<li>' + (m.url ? '<a href="' + href(m.url) + '" target="_blank" rel="noopener noreferrer">' + esc(m.nama) + '</a>' : '<a>' + esc(m.nama) + '</a>') + '</li>';
        }).join(''));
        var v = d.latest_video;
        put('#footer-video', v ? '<a href="' + href(v.url_video) + '" target="_blank" rel="noopener noreferrer"><img src="' + esc(v.thumbnail) + '" alt="' + esc(v.judul) + '" width="100%" /></a>' +
            '<div class="now-playing"><span class="np-icon">&#9654;</span><span>' + esc(v.judul) + '</span></div>' : '<p class="dm-state">Belum ada video.</p>');
    }
    function muatSitus() {
        return ambil('situs.php').then(renderSitus).catch(function () {
            ['#footer-kontak', '#footer-sosmed', '#footer-mitra', '#footer-video'].forEach(function (s) { var el = $(s); if (el) el.innerHTML = ''; });
        });
    }

    /* ───────── Beranda ───────── */
    function halamanBeranda() {
        keadaan('#home-galeri', 'Memuat…'); keadaan('#home-album', 'Memuat…'); keadaan('#home-shop', 'Memuat…');
        keadaan('#home-event', 'Memuat…'); keadaan('#home-about', 'Memuat…');
        ambil('beranda.php').then(function (d) {
            var bagian = function (sel, arr, fn, kosong) { arr.length ? put(sel, arr.map(fn).join('')) : keadaan(sel, kosong); };
            bagian('#home-galeri', d.galeri, function (g) {
                return '<a href="Galery page/gallery.html" class="mini-gallery-item"><div class="mini-gallery-thumb"><img src="' + esc(g.thumbnail) + '" alt="' + esc(g.judul) + '" />' +
                    '<span class="mini-gallery-date">' + esc(tglDM(g.tanggal)) + '</span></div><div class="mini-gallery-info"><span class="mini-gallery-title">' + esc(g.judul) + '</span></div></a>';
            }, 'Belum ada foto.');
            bagian('#home-album', d.album, function (a) {
                return '<div class="mini-album-card"><img src="' + esc(a.cover) + '" alt="' + esc(a.judul) + '" /><div class="mini-album-info"><h4>' + esc(a.judul) + '</h4></div></div>';
            }, 'Belum ada album.');
            bagian('#home-shop', d.produk, function (p) {
                return '<a href="shop/shop.html" class="mini-shop-card"><img src="' + esc(p.gambar) + '" alt="' + esc(p.nama) + '" /><div class="mini-shop-info"><h4>' + esc(p.nama) +
                    '</h4><span class="mini-shop-price">' + esc(rupiahTitik(p.harga)) + '</span></div></a>';
            }, 'Belum ada produk.');
            bagian('#home-event', d.event, function (e) {
                return '<a href="event/index.html#modal-' + e.id + '" class="event-card"><div class="event-card-body"><h3>' + esc(e.judul) + '</h3><p>' + esc(e.lokasi) +
                    '</p><small>' + esc(tglDMY(e.tanggal)) + '</small></div></a>';
            }, 'Belum ada event.');
            bagian('#home-about', d.personel, function (p) {
                return '<div class="about-avatar-card"><img src="' + esc(p.foto) + '" alt="' + esc(p.nama_panggilan) + '" /><span>' + esc(p.nama_panggilan) + '</span></div>';
            }, 'Belum ada data personel.');
        }).catch(function (e) {
            ['#home-galeri', '#home-album', '#home-shop', '#home-event', '#home-about'].forEach(function (s) { keadaan(s, e.message, true); });
        });
    }

    /* ───────── Gallery ───────── */
    function halamanGaleri() {
        keadaan('#tab-photo', 'Memuat…');
        ambil('galeri.php').then(function (d) {
            d.foto.length ? put('#tab-photo', d.foto.map(function (g) {
                return '<div class="gallery-item"><div class="item-thumb"><img src="' + esc(g.thumbnail) + '" alt="' + esc(g.judul) + '" /><span class="item-date">' + esc(tglDM(g.tanggal)) +
                    '</span></div><div class="item-info"><a href="#" class="item-title">' + esc(g.judul) + '</a></div></div>';
            }).join('')) : keadaan('#tab-photo', 'Belum ada foto.');
            d.video.length ? put('#tab-video', d.video.map(function (g) {
                var judul = g.url_video ? '<a href="' + href(g.url_video) + '" target="_blank" rel="noopener noreferrer" class="item-title">' + esc(g.judul) + '</a>' : '<span class="item-title">' + esc(g.judul) + '</span>';
                return '<div class="gallery-item"><div class="item-media video-overlay"><img src="' + esc(g.thumbnail) + '" alt="' + esc(g.judul) + '"></div><div class="item-content">' + judul +
                    '<div class="item-date">' + esc(tglDMY(g.tanggal)) + '</div></div></div>';
            }).join('')) : keadaan('#tab-video', 'Belum ada video.');
        }).catch(function (e) { keadaan('#tab-photo', e.message, true); keadaan('#tab-video', e.message, true); });
    }

    /* ───────── Album ───────── */
    function halamanAlbum() {
        muat('#album-grid', 'album.php', function (d) {
            return d.album.map(function (a) {
                var tracks = a.lagu.map(function (l) {
                    return l.link_spotify ? '<a href="' + href(l.link_spotify) + '" target="_blank" rel="noopener noreferrer">' + esc(l.judul) + '</a>' : '<a>' + esc(l.judul) + '</a>';
                }).join('');
                var detail = a.link_spotify || (a.lagu[0] && a.lagu[0].link_spotify) || null;
                return '<div class="album-card"><div class="image-placeholder"><img src="' + esc(a.cover) + '" alt="' + esc(a.judul) + '" style="width: 100%; height: 100%; object-fit: cover;"></div>' +
                    '<div class="album-info"><p class="release-date">' + esc(tglBulanTahun(a.tanggal_rilis)) + '</p><h3>' + esc(a.judul.toUpperCase()) + '</h3>' +
                    '<div class="track-list-section"><span class="track-label">Track List 💿🎵</span><div class="track-box">' + (tracks || '<a>Belum ada lagu</a>') + '</div></div>' +
                    (detail ? '<a href="' + href(detail) + '" target="_blank" rel="noopener noreferrer" class="detail-link">See more detail -></a>' : '') + '</div></div>';
            }).join('');
        }, 'Belum ada album.');
    }

    /* ───────── Shop ───────── */
    function halamanShop() {
        muat('#shop-grid', 'produk.php', function (d) {
            return d.produk.map(function (p) {
                var tombol = p.tersedia && p.link_beli
                    ? '<a href="' + href(p.link_beli) + '" target="_blank" rel="noopener noreferrer" class="product-btn"><i class="fa-solid fa-cart-shopping"></i> BUY NOW!</a>'
                    : '<span class="product-btn product-btn-habis">SOLD OUT</span>';
                return '<div class="product-item"><div class="product-card-body"><img src="' + esc(p.gambar) + '" alt="' + esc(p.nama) + '" class="product-img"></div>' +
                    '<div class="product-details"><div class="product-price-container"><span class="product-price">' + esc(rupiahKoma(p.harga)) + '</span></div><h3>' + esc(p.nama) + '</h3><p>' + esc(p.deskripsi || '') + '</p>' + tombol + '</div></div>';
            }).join('');
        }, 'Belum ada produk.');
    }

    /* ───────── About ───────── */
    function halamanAbout() {
        muat('#members-grid', 'personel.php', function (d) {
            return d.personel.map(function (p) {
                return '<div class="member-card"><div class="member-avatar"><img src="' + esc(p.foto) + '" alt="' + esc(p.nama_lengkap) + '" /></div><div class="member-name">' + esc(p.nama_panggilan) +
                    '</div><div class="member-role">' + esc(p.peran) + '</div><div class="member-bio">' + esc(p.bio || '') + '</div></div>';
            }).join('');
        }, 'Belum ada data personel.');
    }

    /* ───────── Event ───────── */
    function halamanEvent() {
        var sidebar = $('#event-kategori'), daftar = $('#event-daftar'), modals = $('#event-modal');
        keadaan('#event-daftar', 'Memuat…');
        ambil('event.php').then(function (d) {
            if (!d.kategori.length) { keadaan('#event-daftar', 'Belum ada kategori event.'); return; }
            var byCat = {}; d.event.forEach(function (e) { (byCat[e.kategori_id] = byCat[e.kategori_id] || []).push(e); });

            sidebar.innerHTML = d.kategori.map(function (k) {
                return '<li><label data-kat="' + k.id + '" tabindex="0" role="button">' + esc(k.nama) + ' <span>' + k.jumlah + '</span></label></li>';
            }).join('');
            daftar.innerHTML = d.kategori.map(function (k) {
                var list = byCat[k.id] || [];
                return '<div class="event-group" id="group-' + k.id + '">' + (list.length ? list.map(function (e) {
                    return '<div class="card"><h3>' + esc(e.judul) + '</h3><p>' + esc(e.lokasi) + '</p><small>' + esc((e.tanggal + ' ' + (e.waktu || '')).trim()) + '</small><a href="#modal-' + e.id + '">DETAIL</a></div>';
                }).join('') : '<p class="dm-state">Belum ada event pada kategori ini.</p>') + '</div>';
            }).join('');
            modals.innerHTML = d.event.map(function (e) {
                var row = function (l, v) { return '<div class="modal-row"><span class="modal-label">' + l + '</span><span class="modal-sep">:</span><span class="modal-value">' + v + '</span></div>'; };
                return '<div class="modal" id="modal-' + e.id + '"><div class="modal-box"><div class="modal-header"><h3>' + esc(e.judul) + '</h3><a href="#" class="modal-close">&times;</a></div><div class="modal-body">' +
                    row('Category', esc(e.kategori_nama)) + row('Place', esc(e.lokasi)) + row('Date', esc(tglHari(e.tanggal))) +
                    row('Time', jam(e.waktu) ? esc(jam(e.waktu)) + ' - Finish' : '-') + row('Desc', multiline(e.deskripsi)) + '</div></div></div>';
            }).join('');

            function aktif(id) {
                sidebar.querySelectorAll('label').forEach(function (l) { l.classList.toggle('active', l.getAttribute('data-kat') === String(id)); });
                daftar.querySelectorAll('.event-group').forEach(function (g) { g.classList.toggle('active', g.id === 'group-' + id); });
            }
            sidebar.addEventListener('click', function (ev) { var l = ev.target.closest('label[data-kat]'); if (l) aktif(l.getAttribute('data-kat')); });
            sidebar.addEventListener('keydown', function (ev) {
                var l = ev.target.closest('label[data-kat]');
                if (l && (ev.key === 'Enter' || ev.key === ' ')) { ev.preventDefault(); aktif(l.getAttribute('data-kat')); }
            });

            var kategoriAwal = d.kategori[0].id, hash = window.location.hash;
            var m = /^#modal-(\d+)$/.exec(hash);
            if (m) { d.event.forEach(function (e) { if (String(e.id) === m[1]) kategoriAwal = e.kategori_id; }); }
            aktif(kategoriAwal);
            // Elemen modal baru dibuat setelah halaman dibuka: aktifkan ulang :target agar tautan dari beranda (#modal-ID) langsung membuka detailnya
            if (m && document.getElementById('modal-' + m[1])) { history.replaceState(null, '', window.location.pathname + window.location.search); window.location.hash = hash; }
        }).catch(function (e) { keadaan('#event-daftar', e.message, true); });
    }

    /* ───────── Be a Fans ───────── */
    function halamanFans() {
        var form = $('#fanForm'), msg = $('#msg'); if (!form) return;
        var tombol = form.querySelector('.btn-submit');
        function tampil(teks, ok) { msg.textContent = teks; msg.className = 'msg ' + (ok ? 'ok' : 'err'); }
        var resetOtomatis = false; // reset setelah sukses kirim tidak boleh menghapus pesan "terima kasih"
        form.addEventListener('reset', function () {
            var otomatis = resetOtomatis; resetOtomatis = false;
            setTimeout(function () { if (window.fillKota) window.fillKota(); if (!otomatis) msg.className = 'msg'; }, 0);
        });
        form.addEventListener('submit', function (e) {
            e.preventDefault();
            var f = function (n) { return (form.elements[n] && form.elements[n].value || '').trim(); };
            var jk = form.querySelector('input[name="gender"]:checked');
            var data = {
                nama_lengkap: f('nama'), jenis_kelamin: jk ? jk.value : '', tanggal_lahir: f('tgl_lahir'),
                alamat: f('kota') ? f('kota') + ', ' + f('provinsi') : f('provinsi'),
                email: f('email'), no_telepon: f('telepon'), url_sosmed: f('sosmed'), website: f('website')
            };
            if (!data.nama_lengkap || !/^\S+@\S+\.\S+$/.test(data.email)) return tampil('Isi nama lengkap dan alamat email yang valid.', false);
            if (!data.jenis_kelamin) return tampil('Pilih jenis kelamin.', false);
            if (!data.tanggal_lahir) return tampil('Isi tanggal lahir.', false);
            if (!data.no_telepon) return tampil('Isi nomor telepon.', false);
            tombol.disabled = true; tombol.textContent = 'Mengirim…';
            fetch(API + '/fans.php', { method: 'POST', headers: { 'Content-Type': 'application/json' }, body: JSON.stringify(data) })
                .then(function (r) { return r.json().then(function (d) { return { r: r, d: d }; }, function () { throw new Error('Respons server tidak valid.'); }); })
                .then(function (x) {
                    if (!x.r.ok || !x.d.ok) throw new Error(x.d.error || 'Pendaftaran gagal.');
                    resetOtomatis = true; form.reset(); tampil(x.d.pesan, true);
                })
                .catch(function (err) { tampil(err.message === 'Failed to fetch' ? 'Tidak dapat terhubung ke server.' : err.message, false); })
                .then(function () { tombol.disabled = false; tombol.textContent = 'Submit'; });
        });
    }

    /* ───────── Mulai ───────── */
    var HALAMAN = { home: halamanBeranda, gallery: halamanGaleri, album: halamanAlbum, shop: halamanShop, about: halamanAbout, event: halamanEvent, fans: halamanFans };
    function mulai() {
        muatSitus();
        var fn = HALAMAN[document.body.getAttribute('data-page')];
        if (fn) fn();
    }
    if (document.readyState === 'loading') document.addEventListener('DOMContentLoaded', mulai); else mulai();
})();
