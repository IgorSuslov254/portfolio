<section id="third_block_next">
	<div class="container">

		<!-- title -->
		<div class="row text-start text-sm-center tbnTitle">
			<h1>{!!__('plaster/third_block_next.h1')!!}</h1>
			<h2 class="mt-4 d-none d-sm-block">{!!__('plaster/third_block_next.h2')!!}</h2>
            <h2 class="d-block d-sm-none">Оставьте номер. <br> Перезвоним за <span class="default_color fw-bold">3 минуты</span></h2>
		</div>

		<!-- form -->
		<form action="#" method="post" class="row">
			@csrf

			<div class="col-sm-6 offset-lg-2 col-lg-4">
				@foreach (__('plaster/third_block_next.feedback') as $key => $feedback)
					@break($loop->index > 0)

					@if (!empty($feedback['text']))
						<label for="{!!$feedback['id']!!}">{!!$feedback['text']!!}</label>
					@endif

					<input
						type="{!!$feedback['type']!!}"
						@if (!empty($feedback['placeholder']))
						 	placeholder="{!!$feedback['placeholder']!!}"
						@endif
						@if (!empty($feedback['mask']))
							data-mdb-input-mask="{!!$feedback['mask']!!}"
						@endif
						id="{!!$feedback['id']!!}"
						name="{!!$feedback['name']!!}"
						@if (!empty($feedback['class']))
							class="{!! $feedback['class'] !!}"
						@endif
						data-mdb-mask-placeholder="true"
						required
					>
				@endforeach
			</div>
			<div class="col-sm-6 col-lg-4">
				<!-- button -->
				<button type="submit" class="btn btn-warning btn-rounded mt-2">
					<svg class="me-1" width="26" height="25" viewBox="0 0 26 25" fill="none" xmlns="http://www.w3.org/2000/svg">
						<path d="M15.5218 4.36269C14.9268 4.25277 14.3803 4.63249 14.267 5.21332C14.1537 5.79415 14.5347 6.36124 15.1135 6.47491C16.8563 6.81466 18.2019 8.16369 18.543 9.91242V9.91367C18.6401 10.4171 19.0833 10.783 19.5936 10.783C19.6621 10.783 19.7306 10.7768 19.8003 10.7643C20.3791 10.6481 20.7601 10.0823 20.6468 9.50022C20.1376 6.88836 18.1272 4.87107 15.5218 4.36269Z" fill="white"/>
						<path d="M15.4467 0.00991184C15.1679 -0.0300592 14.8878 0.0523811 14.665 0.229753C14.4359 0.409623 14.2928 0.669435 14.2616 0.960474C14.1957 1.5488 14.6202 2.08091 15.2077 2.14712C19.2596 2.59929 22.4091 5.75575 22.8647 9.82031C22.9257 10.3649 23.3825 10.7759 23.9278 10.7759C23.9688 10.7759 24.0087 10.7734 24.0498 10.7684C24.3348 10.7372 24.5888 10.596 24.768 10.3712C24.946 10.1463 25.0269 9.86653 24.9946 9.58049C24.4269 4.50916 20.502 0.573254 15.4467 0.00991184Z" fill="white"/>
						<path fill-rule="evenodd" clip-rule="evenodd" d="M11.2893 13.715C16.2754 18.6998 17.4066 12.933 20.5813 16.1055C23.642 19.1653 25.4011 19.7783 21.5233 23.6551C21.0375 24.0454 17.9514 28.7418 7.10549 17.899C-3.74171 7.05479 0.951946 3.96546 1.34241 3.47985C5.22964 -0.407632 5.83211 1.36172 8.89279 4.42154C12.0675 7.59539 6.30309 8.73024 11.2893 13.715Z" fill="white"/>
					</svg>


					<span>{!!__('plaster/third_block_next.button')!!}</span>
				</button>
			</div>

			<div class="col-12 mt-4 mt-sm-0 text-center">
				<div class="form-check">
					@foreach (__('plaster/third_block_next.feedback') as $key => $feedback)
						@continue($loop->index != 1)

						<input
							type="{!!$feedback['type']!!}"
							@if (!empty($feedback['placeholder']))
							 	placeholder="{!!$feedback['placeholder']!!}"
							@endif
							@if (!empty($feedback['mask']))
								data-mdb-input-mask="{!! $feedback['mask'] !!}"
							@endif
							id="{!!$feedback['id']!!}"
							name="{!!$feedback['id']!!}"
							@if (!empty($feedback['class']))
								class="{!!$feedback['class']!!}"
							@endif
							checked
							required
						>

						@if (!empty( $feedback['text'] ))
							<label class="form-check-label text-white" for="{!!$feedback['id']!!}">{!!$feedback['text']!!}</label>
						@endif
					@endforeach
				</div>
			</div>
		</form>
	</div>
</section>
