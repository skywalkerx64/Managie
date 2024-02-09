<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
<div style="margin-bottom: 2rem; ">
    <h2>Rapport d'éligibilité par classement</h2>
    @foreach ($stars as $key => $star)
    <div>
        <h3>
            <span style="padding: 0.5rem; background-color: {{$star['is_eligible'] ? '#286D64' : '#f44336'}}; border-radius: 4px; color: azure">{{$key}}</span>
            {{ ' ' . ($star['is_eligible'] ? 'ELIGIBLE' :'NON ELIGIBLE') }}
        </h3>
    </div>
    <div style="">
        @foreach ($star['chapters'] as $chapter)
        <table style="width: 100%; border: 1px solid #ccc; border-radius: 4px; margin-bottom: 1rem;">
            <thead style="background-color: #286D64; color: azure; border-radius: 4px;">
                <tr>
                    <th style="text-align: left;">{{$chapter['label']}}</th>
                    <th style="text-align: left;">{{$chapter['percents']['major']}} %</th>
                    <th style="text-align: left;">{{$chapter['percents']['minor']}} %</th>
                </tr>
            </thead>
            <tbody>
                @foreach ($chapter['sections'] as $section)
                <tr style="border: 1px solid #ccc;">
                    <td>{{ $section['label'] }}</td>
                    <td>{{ $section['percents']['major']}} %</td>
                    <td>{{ $section['percents']['minor']}} %</td>
                </tr>
                <tr><span style="visibility: hidden;">ooooooooo</span></tr>
                @endforeach
            </tbody>
        </table>
        @endforeach
    </div>
    @endforeach
</div>