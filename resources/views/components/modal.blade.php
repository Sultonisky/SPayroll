@props([
    'id',
    'title' => 'Confirm Action',
    'type' => 'danger',
    'actionUrl' => '',
    'method' => 'POST',
    'confirmText' => 'Confirm',
    'cancelText' => 'Cancel',
    'icon' => 'fa-exclamation-triangle',
    'size' => '',
])

<div class="modal fade" id="{{ $id }}" tabindex="-1" aria-labelledby="{{ $id }}Label"
    aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered modal-sm-down {{ $size }}">
        <div class="modal-content border-0 shadow-lg mx-3 mx-md-0">
            <div class="modal-header bg-{{ $type }} text-white border-0">
                <h5 class="modal-title d-flex align-items-center" id="{{ $id }}Label">
                    <i class="fas {{ $icon }} me-2"></i>{{ $title }}
                </h5>
                <button type="button" class="btn-close btn-close-white" data-coreui-dismiss="modal"
                    data-bs-dismiss="modal" data-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body text-center py-4 px-3">
                <div class="px-md-4">
                    {{ $slot }}
                </div>
            </div>
            <div class="modal-footer border-0 justify-content-center pb-4 px-3">
                <div class="d-grid gap-2 d-md-flex justify-content-md-center w-100">
                    <button type="button" class="btn btn-secondary text-white rounded-pill px-4 border"
                        data-coreui-dismiss="modal" data-bs-dismiss="modal"
                        data-dismiss="modal">{{ $cancelText }}</button>

                    @if ($actionUrl)
                        <form action="{{ $actionUrl }}" method="POST" class="d-grid d-md-inline">
                            @csrf
                            @if (in_array(strtoupper($method), ['PUT', 'PATCH', 'DELETE']))
                                @method($method)
                            @endif
                            <button type="submit"
                                class="btn btn-{{ $type }} text-white rounded-pill px-4 shadow-sm fw-bold">
                                {{ $confirmText }}
                            </button>
                        </form>
                    @else
                        {{ $footer ?? '' }}
                    @endif
                </div>
            </div>
        </div>
    </div>
</div>
