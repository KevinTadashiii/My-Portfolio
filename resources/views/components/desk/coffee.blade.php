<div class="coffee" x-data="{ joke: false }" :class="joke && 'is-squeezed'">
    <div class="coffee__object">
        <p class="coffee__receipt">
            <span class="coffee__receipt-shop">es kopi susu</span>
            <span>sugar 73%</span>
            <span>ice 3</span>
            <span class="coffee__receipt-total">Rp 18.000</span>
        </p>

        <button
            type="button"
            class="coffee__bag"
            @click="joke = !joke"
            :aria-expanded="joke.toString()"
            aria-controls="coffee-joke"
            aria-describedby="coffee-joke"
            aria-label="Bag of iced coffee — reveal the caffeine reading"
        >
            <svg class="coffee__svg" viewBox="0 0 160 122" role="presentation" aria-hidden="true" focusable="false">
                <defs>
                    <!-- The liquid is the bag silhouette scaled to 98.5% about the bag
                         centre, so the plastic wall reads as only ~1.5% wider than the
                         drink. The surface clip keeps a flat liquid level at y=58. -->
                    <clipPath id="coffee-liquid-clip">
                        <path transform="translate(80 74) scale(0.985) translate(-80 -74)" d="M80 28C69 28 61 33 53 41C41 52 31 64 26 78C21 90 21 103 26 111C31 117 42 120 56 120C64 120 72 120 80 120C88 120 96 120 104 120C118 120 129 117 134 111C139 103 139 90 134 78C129 64 119 52 107 41C99 33 91 28 80 28Z"/>
                    </clipPath>
                    <clipPath id="coffee-liquid-surface">
                        <rect x="0" y="58" width="160" height="64"/>
                    </clipPath>
                    <clipPath id="coffee-straw-clip">
                        <rect x="74.5" y="0" width="9" height="34" rx="4.5"/>
                    </clipPath>
                </defs>

                <!-- Squeeze the whole bag drawing 25% horizontally about its centre
                     axis, so the bag gets narrower without changing its height. -->
                <g class="coffee__bag-shape" transform="translate(80 0) scale(0.75 1) translate(-80 0)">
                    <path class="coffee__plastic-body" d="M80 28C69 28 61 33 53 41C41 52 31 64 26 78C21 90 21 103 26 111C31 117 42 120 56 120C64 120 72 120 80 120C88 120 96 120 104 120C118 120 129 117 134 111C139 103 139 90 134 78C129 64 119 52 107 41C99 33 91 28 80 28Z"/>

                    <g clip-path="url(#coffee-liquid-clip)">
                        <g clip-path="url(#coffee-liquid-surface)">
                            <rect class="coffee__liquid" x="0" y="52" width="160" height="70"/>
                            <path class="coffee__milk" d="M0 50H160V84C151 92 142 82 133 89C124 96 114 85 105 92C96 99 86 88 77 95C68 102 58 90 49 96C40 102 31 91 22 97C13 103 6 94 0 98Z"/>
                            <path class="coffee__coffee-swirl" d="M38 78C50 70 62 82 74 76C86 70 98 80 110 73C116 70 120 72 122 75"/>
                            <path class="coffee__milk-sheen" d="M50 67C62 63 74 69 86 64C96 60 106 63 116 61"/>
                            <path class="coffee__milk-streak" d="M36 106C52 98 64 110 80 104C96 98 108 107 124 100"/>
                            <path class="coffee__surface" d="M46 60C58 56 70 62 82 59C94 56 104 61 115 58"/>
                            <path class="coffee__liquid-shade" d="M0 105C30 113 50 118 80 118C110 118 130 113 160 105V122H0Z"/>

                            <g class="coffee__ice">
                                <g transform="rotate(-12 54 77)">
                                    <rect class="coffee__ice-cube" x="43" y="66" width="22" height="22" rx="4"/>
                                    <path class="coffee__ice-highlight" d="M47 70C50 73 52 76 53 80"/>
                                </g>
                                <g transform="rotate(9 88 73)">
                                    <rect class="coffee__ice-cube" x="76" y="61" width="24" height="24" rx="5"/>
                                    <path class="coffee__ice-highlight" d="M81 66C84 69 86 72 87 77"/>
                                </g>
                                <g transform="rotate(-17 108 87)">
                                    <rect class="coffee__ice-cube" x="99" y="78" width="18" height="18" rx="3"/>
                                    <path class="coffee__ice-highlight" d="M103 82C105 84 107 87 108 90"/>
                                </g>
                            </g>
                        </g>
                    </g>

                    <path class="coffee__plastic-edge" d="M80 28C69 28 61 33 53 41C41 52 31 64 26 78C21 90 21 103 26 111C31 117 42 120 56 120C64 120 72 120 80 120C88 120 96 120 104 120C118 120 129 117 134 111C139 103 139 90 134 78C129 64 119 52 107 41C99 33 91 28 80 28Z"/>

                    <path class="coffee__plastic-gloss" d="M44 64C50 52 59 43 70 37"/>
                    <path class="coffee__plastic-gloss coffee__plastic-gloss--soft" d="M120 66C126 78 130 92 127 103"/>
                    <path class="coffee__plastic-gloss coffee__plastic-gloss--soft" d="M38 108C52 115 66 117 82 117"/>

                    <path class="coffee__gather" d="M63 43C70 47 90 47 97 43"/>
                    <path class="coffee__fold coffee__fold--soft" d="M65 43C58 48 53 55 50 62"/>
                    <path class="coffee__fold coffee__fold--soft" d="M95 43C102 48 107 55 110 62"/>
                    <path class="coffee__fold coffee__fold--soft" d="M72 39C68 43 65 48 63 53"/>
                    <path class="coffee__fold coffee__fold--soft" d="M88 39C92 43 95 48 97 53"/>
                    <path class="coffee__fold coffee__fold--soft" d="M54 45C48 50 44 56 42 62"/>
                    <path class="coffee__fold coffee__fold--soft" d="M106 45C112 50 116 56 118 62"/>
                    <path class="coffee__fold coffee__fold--soft" d="M36 94C43 98 51 98 58 96"/>
                    <path class="coffee__fold coffee__fold--soft" d="M124 94C117 98 109 98 102 96"/>

                    <path class="coffee__seam" d="M30 107C45 114 62 117 80 117C98 117 115 114 130 107"/>
                    <path class="coffee__seam coffee__seam--soft" d="M34 111C48 117 64 119 80 119C96 119 112 117 126 111"/>

                    <g class="coffee__straw">
                        <rect class="coffee__straw-body" x="74.5" y="0" width="9" height="34" rx="4.5"/>
                        <g clip-path="url(#coffee-straw-clip)">
                            <path class="coffee__straw-stripe" d="M73 8L85 4"/>
                            <path class="coffee__straw-stripe" d="M73 15L85 11"/>
                            <path class="coffee__straw-stripe" d="M73 22L85 18"/>
                        </g>
                        <ellipse class="coffee__straw-mouth" cx="79" cy="1.2" rx="4.2" ry="1.6"/>
                    </g>

                    <path class="coffee__flap" d="M70 39C62 36 54 37 49 42C55 47 63 47 70 43C72 42 72 40 70 39Z"/>
                    <path class="coffee__flap" d="M90 39C98 36 106 37 111 42C105 47 97 47 90 43C88 42 88 40 90 39Z"/>
                    <path class="coffee__flap-fold" d="M54 40C60 42 66 43 70 42"/>
                    <path class="coffee__flap-fold" d="M106 40C100 42 94 43 90 42"/>

                    <path class="coffee__neck" d="M70 39C68 30 70 23 80 23C90 23 92 30 90 39C89 45 85 47 80 47C75 47 71 45 70 39Z"/>
                    <path class="coffee__knot-highlight" d="M73 30C75 26 81 24 86 27"/>
                    <path class="coffee__knot-fold" d="M76 26C75 31 75 36 76 41"/>
                    <path class="coffee__knot-fold" d="M84 26C85 31 85 36 84 41"/>
                    <path class="coffee__knot-fold" d="M70 41C65 43 61 44 56 44"/>
                    <path class="coffee__knot-fold" d="M90 41C95 43 99 44 104 44"/>

                    <path class="coffee__band" d="M71.5 27.5C75 30.5 85 30.5 88.5 27.5"/>
                    <path class="coffee__band coffee__band--second" d="M70.5 34C75 37 85 37 89.5 34"/>
                    <ellipse class="coffee__band-knot" cx="90.5" cy="31" rx="2.6" ry="3.8" transform="rotate(-18 90.5 31)"/>
                    <path class="coffee__band-tail" d="M92 32C95 34 97 36 99 39"/>
                    <path class="coffee__band-tail" d="M92 34C94 37 95 40 96 43"/>

                    <g class="coffee__beads">
                        <path class="coffee__sweat" d="M33 83C34 86 34 88 33 90"/>
                        <path class="coffee__sweat" d="M126 96C127 99 127 101 126 103"/>
                        <circle class="coffee__bead-static" cx="31" cy="90" r="1.7"/>
                        <circle class="coffee__bead-static" cx="36" cy="104" r="1.2"/>
                        <circle class="coffee__bead-static" cx="128" cy="92" r="1.4"/>
                        <circle class="coffee__bead-static" cx="118" cy="107" r="1.8"/>
                        <circle class="coffee__bead" cx="88" cy="112" r="2.2"/>
                        <circle class="coffee__bead-static" cx="47" cy="112" r="1.1"/>
                        <circle class="coffee__bead-static" cx="73" cy="116" r="1"/>
                    </g>
                </g>
            </svg>
        </button>
    </div>

    <p class="coffee__joke" id="coffee-joke" :class="joke && 'is-visible'">
        caffeine_level: nominal
    </p>
</div>
