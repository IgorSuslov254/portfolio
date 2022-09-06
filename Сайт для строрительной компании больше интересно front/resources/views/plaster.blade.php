<!DOCTYPE html>
<html lang="{{app()->getLocale()}}">
<head>
	<meta charset="UTF-8">
	<meta name="viewport" content="width=device-width, initial-scale=1.0">
	<meta http-equiv="x-ua-compatible" content="ie=edge">
	<meta name="description" content="Механизированная штукатурка. 3 года гарантии. Готово под обои. Оплата в рассрочку."/>
	<meta name="keywords" content="штукатурка в краснодаре, механизированная штукатурка в краснодаре, машинная штукатурка в краснодаре, штукатурка стен в краснодаре"/>
	<meta name="yandex-verification" content="bae5863557b495b8" />

	<title>{{__('plaster/first_block.title')}}</title>

	@include('plaster.style')
</head>
<body>
	<script>
		const fb_h2 = {!!json_encode(__('plaster/first_block.h2'))!!};
	</script>
    @if($params['onlyMap'])
        <div style="margin-top: 17rem !important;">
            @include('plaster.sixth_block')
        </div>
    @else
        @include('plaster.spinner')
        @include('plaster.first_block')
        @include('plaster.second_block')
        {{--@include('plaster.third_block')--}}
        @include('plaster.calk')
        @include('plaster.bestPriceGuarantee')
        @include('plaster.new_fourth_block')
        @include('plaster.third_block_next')
        @include('plaster.fifth_block')
        @include('plaster.sixth_block')
        @include('plaster.questions')
        @include('plaster.seventh_block')
        @include('plaster.error')
        @include('plaster.successCallbackModal')
    @endif

    @include('plaster.script')
</body>
</html>
