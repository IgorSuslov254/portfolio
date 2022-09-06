{{-- googletagmanager --}}
<script>
    (function(w,d,s,l,i){w[l]=w[l]||[];w[l].push({'gtm.start':
    new Date().getTime(),event:'gtm.js'});var f=d.getElementsByTagName(s)[0],
    j=d.createElement(s),dl=l!='dataLayer'?'&l='+l:'';j.async=true;j.src=
    'https://www.googletagmanager.com/gtm.js?id='+i+dl;f.parentNode.insertBefore(j,f);
    })(window,document,'script','dataLayer','GTM-599TMWR');
</script>

{{-- amo_social_button --}}
<script>
    (function(a,m,o,c,r,m){a[m]={id:"247009",hash:"49e320937071f1f6fa305fe1aa8f16c2e9e8a90b41850b75275770a1018847b1",locale:"ru",inline:false,setMeta:function(p){this.params=(this.params||[]).concat([p])}};a[o]=a[o]||function(){(a[o].q=a[o].q||[]).push(arguments)};var d=a.document,s=d.createElement('script');s.async=true;s.id=m+'_script';s.src='https://gso.amocrm.ru/js/button.js?1659689668';d.head&&d.head.appendChild(s)}(window,0,'amoSocialButton',0,0,'amo_social_button'));
</script>

<!-- jQuery -->
<script type="text/javascript"src="{{ url('/data/style/js/library/jquery-3.6.0.min.js') }}"></script>

<!-- MDB -->
<script type="text/javascript" src="{{ url('/data/mdb/js/mdb.min.js') }}" async></script>
<script type="text/javascript" src="{{ url('/data/style/js/library/multi-carousel.min.js') }}" async></script>
<script type="text/javascript" src="{{ url('/data/style/js/library/inputmask.min.js') }}" async></script>

<!-- typed -->
<script src="{{ url('/data/style/js/library/typed.js') }}" async></script>

<!-- my js -->
<script type="text/javascript" src="{{ url('/data/style/js/default/default.js') }}" async></script>

@if(!$params['onlyMap'])
    <script type="text/javascript" src="{{ url('/data/style/js/plaster/first_block.js') }}" async></script>
    <script type="text/javascript" src="{{ url('/data/style/js/plaster/second_block.js') }}" async></script>
{{--    <script type="text/javascript" src="{{ url('/data/style/js/plaster/third_block.js') }}" async></script>--}}
    <script type="text/javascript" src="{{ url('/data/style/js/plaster/calk.js') }}" async></script>
    <script type="text/javascript" src="{{ url('/data/style/js/plaster/third_block_next.js') }}" async></script>
    <script type="text/javascript" src="{{ url('/data/style/js/plaster/new_fourth_block.js') }}" async></script>
    <script type="text/javascript" src="{{ url('/data/style/js/plaster/bestPriceGuarantee.js') }}" async></script>
    <script type="text/javascript" src="{{ url('/data/style/js/plaster/fifth_block.js') }}" async></script>
@endif

<script type="text/javascript" src="{{ url('/data/style/js/plaster/sixth_block.js') }}" async></script>
