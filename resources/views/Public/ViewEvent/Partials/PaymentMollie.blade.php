<div class="event_order_form">
    {!! Form::open(['url' => route('postCreateOrder', ['event_id' => $event->id ]), 'class' => 'ajax payment-form']) !!}

    {!! Form::hidden('event_id', $event->id) !!}

    @if($event->pre_order_display_message)
    <div class="well well-small">
        {!! nl2br(e($event->pre_order_display_message)) !!}
    </div>
    @endif

    {!! Form::submit(trans("Public_ViewEvent.checkout_order"), ['class' => 'btn btn-lg btn-success card-submit', 'style' => 'width:100%;']) !!}
    {!! Form::close() !!}
</div>

<style type="text/css">


</style>
