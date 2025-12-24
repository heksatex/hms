<style>

    /* Dropdown */
    .select2-results__option {
        white-space: nowrap !important;
        overflow-x: auto !important;
        text-overflow: clip !important;
        -webkit-overflow-scrolling: touch;
    }
    /* Selected element */
    .select2-selection__rendered {
        white-space: nowrap !important;
        overflow: hidden !important;
        text-overflow: ellipsis !important;
    }

    /* Buat dropdown melebar sesuai isi */
    .select2-container--open .select2-dropdown {
        width: auto !important;       /* biarkan melebar */
        min-width: 200px;             /* kasih batas minimal (opsional) */
        max-width: none !important;   /* hilangkan batas maksimum */
    }


    /* Agar tampilan dropdown tidak melebar terlalu jauh di HP */
    @media screen and (max-width: 768px) {
        .select2-container--open .select2-dropdown {
            width: 100% !important;
            max-width: 100% !important;
            overflow-x: auto !important;
        }
    }
</style>