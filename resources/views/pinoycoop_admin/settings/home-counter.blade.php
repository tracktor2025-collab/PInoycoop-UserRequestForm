@extends('pinoycoop_admin.layouts.app', ['title' => 'Home Counter Settings'])

@section('content')
    <style>
        .counter-editor-grid {
            display: grid;
            gap: 1rem;
        }

        .counter-editor-card {
            display: grid;
            grid-template-columns: 1fr 140px 1fr;
            gap: .9rem;
            padding: 1rem;
            border: 1px solid #dbe5ee;
            border-radius: 14px;
            background: linear-gradient(180deg, #fbfdff, #f4f8fb);
        }

        .counter-help {
            color: #6a7d92;
            font-size: .86rem;
            line-height: 1.5;
            margin: .35rem 0 0;
        }

        @media (max-width: 900px) {
            .counter-editor-card {
                grid-template-columns: 1fr;
            }
        }
    </style>

    <div class="top">
        <h2>Home Counter</h2>
        <a class="btn btn-g" href="{{ route('landing') }}" target="_blank">View Website</a>
    </div>

    <div class="card">
        <div class="head">Homepage Counter Section</div>
        <div class="body">
            <form method="POST" action="{{ route('pinoycoop.admin.settings.home-counter.update') }}">
                @csrf
                @method('PUT')

                <div class="counter-editor-grid">
                    @foreach ($counters as $index => $counter)
                        <div class="counter-editor-card">
                            <label>Label
                                <input type="text" name="counters[{{ $index }}][label]" value="{{ old('counters.' . $index . '.label', $counter['label'] ?? '') }}" required>
                            </label>

                            <label>Number
                                <input type="number" name="counters[{{ $index }}][value]" value="{{ old('counters.' . $index . '.value', $counter['value'] ?? 0) }}" min="0" max="999999" required>
                            </label>

                            <label>Icon class
                                <input type="text" name="counters[{{ $index }}][icon]" value="{{ old('counters.' . $index . '.icon', $counter['icon'] ?? 'icofont-heart') }}" required>
                            </label>
                        </div>
                    @endforeach
                </div>

                <p class="counter-help">Use IcoFont icon class names like <strong>icofont-heart</strong>, <strong>icofont-rocket</strong>, <strong>icofont-hand-power</strong>, or <strong>icofont-shield-alt</strong>.</p>

                <div style="margin-top:1rem;">
                    <button class="btn btn-p" type="submit">Save Counter Section</button>
                </div>
            </form>
        </div>
    </div>
@endsection
