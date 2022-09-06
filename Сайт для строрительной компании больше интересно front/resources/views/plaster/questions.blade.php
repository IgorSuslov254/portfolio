<section id="questions">
    <div class="container">
        <h1>Часто задаваемые <span class="default_color">вопросы</span></h1>

        <div class="accordion accordion-flush" id="accordionFlushQuestions">
            @foreach($params['DB']['questions'] as $question)
                <div class="accordion-item">
                    <h2 class="accordion-header" id="flush-heading{!! $question->id !!}">
                        <button
                            class="accordion-button collapsed"
                            type="button"
                            data-mdb-toggle="collapse"
                            data-mdb-target="#flush-collapse{!! $question->id !!}"
                            aria-expanded="false"
                            aria-controls="flush-collapse{!! $question->id !!}"
                        >
                            {!! $question->question !!}
                        </button>
                    </h2>
                    <div
                        id="flush-collapse{!! $question->id !!}"
                        class="accordion-collapse collapse"
                        aria-labelledby="flush-heading{!! $question->id !!}"
                        data-mdb-parent="#accordionFlushQuestions"
                    >
                        <div class="accordion-body">
                            {!! $question->answer !!}
                        </div>
                    </div>
                </div>
            @endforeach
        </div>
    </div>
</section>
