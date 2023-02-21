<!DOCTYPE html>
<html>

<head>
    <meta charset="UTF-8" />
    <style>
        <?php
            echo file_get_contents(asset('css/mail_style.css'));
        ?>
    </style>
</head>

<body>
    <div class="exception-summary">
        <div class="container">
            <div class="exception-message-wrapper">
                <h1 class="break-long-words exception-message">{{ $content['message'] ?? '' }}</h1>
                <div class="exception-illustration hidden-xs-down"></div>
            </div>
        </div>
    </div>

    <div class="container">
        <div class="trace trace-as-html">
            <table class="trace-details">
                <thead class="trace-head">
                    <tr>
                        <th>
                            <h3 class="trace-class">
                                <span class="text-muted">(1/1)</span>
                                <span class="exception_title"><span title="ErrorException">ErrorException</span></span>
                            </h3>
                            <p class="break-long-words trace-message">{{ $content['message'] ?? '' }}</p>
                            <p class="">URL: {{ $content['url'] ?? '' }}</p>
                            <p class="">IP: {{ $content['ip'] ?? '' }}</p>
                        </th>
                    </tr>
                </thead>

                <tbody>
                    <tr>
                        <td>
                            <span class="block trace-file-path">in <span
                                    title="{{ $content['file'] ?? '' }}"><strong>{{ $content['file'] ?? '' }}</strong>line
                                    {{ $content['line'] ?? '' }}</span></span>
                        </td>
                    </tr>

                    @foreach ($content['trace'] ?? [] as $value)
                        <tr>

                            <td>

                                at <span class="trace-class"><span
                                        title="{{ $value['class'] ?? '' }}">{{ basename($value['class'] ?? '') }}</span></span><span
                                    class="trace-type">-></span><span
                                    class="trace-method">{{ $value['function'] ?? '' }}</span>(<span
                                    class="trace-arguments"></span>)<span class="block trace-file-path">in <span
                                        title=""><strong>{{ $value['file'] ?? '' }}</strong> line
                                        {{ $value['line'] ?? '' }}</span></span>

                            </td>

                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </div>
</body>

</html>
