@php
    $records = $records ?? [];
    $tableName = $tableName ?? '';
    $parameters = $parameters ?? [];
@endphp

<div class="p-4">
    <div class="mb-4">
        <h2 class="text-lg font-medium text-gray-900">Table: {{ $tableName }}</h2>
        <p class="text-sm text-gray-500">Total Records: {{ count($records) }}</p>
    </div>

    @if(count($records) > 0)
        <div class="overflow-x-auto">
            <table class="min-w-full divide-y divide-gray-200">
                <thead class="bg-gray-50">
                    <tr>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">ID</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">User ID</th>
                        @foreach($parameters as $param)
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                {{ $param['name'] }}
                                <div class="text-xs text-gray-400">
                                    {{ $param['type'] }}
                                    @if($param['nullable'])
                                        (nullable)
                                    @endif
                                    @if($param['unique'])
                                        (unique)
                                    @endif
                                </div>
                            </th>
                        @endforeach
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Created At</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Updated At</th>
                    </tr>
                </thead>
                <tbody class="bg-white divide-y divide-gray-200">
                    @foreach($records as $record)
                        <tr>
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">{{ $record->id }}</td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">{{ $record->user_id }}</td>
                            @foreach($parameters as $param)
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                                    @if($record->{$param['name']} === null)
                                        <span class="text-gray-400">NULL</span>
                                    @else
                                        {{ $record->{$param['name']} }}
                                    @endif
                                </td>
                            @endforeach
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">{{ $record->created_at }}</td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">{{ $record->updated_at }}</td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    @else
        <div class="text-center py-4">
            <p class="text-gray-500">No records found in this database.</p>
        </div>
    @endif
</div>