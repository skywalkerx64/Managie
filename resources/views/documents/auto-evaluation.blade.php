<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
<div style="margin-bottom: 2rem; ">
    @foreach ($chapters as $chapter)
    <div>
        <h3>Chapitre {{ $loop->index + 1 }} : {{ $chapter['label'] }}</h3>
    </div>
    <div style="">
        @foreach ($chapter['sections'] as $section)
        <table style="width: 100%; border: 1px solid #ccc; border-radius: 4px; margin-bottom: 1rem;">
            <thead style="background-color: #286D64; color: azure; border-radius: 4px;">
                <tr>
                    <th style="text-align: left;">QUESTION</th>
                    <th style="text-align: left;"></th>
                    <th style="text-align: left;">REPONSE</th>
                </tr>
            </thead>
            <tbody>
                @foreach ($section['questions'] as $question)
                <tr style="border: 1px solid #ccc;">
                    <td>{{ $question['label'] }}</td>
                    <td><span style="visibility: hidden;">oooooooooooooo</span></td>
                    <td>{{ $question['response'] }}</td>
                </tr>
                <tr><span style="visibility: hidden;">oooooooooooooo</span></tr>
                @endforeach
            </tbody>
        </table>
        @endforeach
    </div>
    @endforeach
</div>