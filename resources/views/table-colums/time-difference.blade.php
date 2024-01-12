
<td>
@php
    $startDate = new \DateTime($record['start_time']);
    $endDate = new \DateTime($record['answer_time']);

    // Calculate the time difference
    $interval = $startDate->diff($endDate);

    // Display the difference in a readable format
    echo $interval->format('%H:%I:%S');
@endphp
</td>
