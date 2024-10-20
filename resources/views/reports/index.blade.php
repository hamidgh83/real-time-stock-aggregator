@extends('layouts.default')

@section('content')

<div class="overflow-x-auto">
    <table class="min-w-full divide-y-2 divide-gray-200 bg-white text-sm">
      <thead class="ltr:text-left rtl:text-right">
        <tr>
          <th class="whitespace-nowrap text-left px-4 py-2 font-medium text-gray-900">Code</th>
          <th class="whitespace-nowrap text-left px-4 py-2 font-medium text-gray-900">Name</th>
          <th class="whitespace-nowrap text-left px-4 py-2 font-medium text-gray-900">High</th>
          <th class="whitespace-nowrap text-left px-4 py-2 font-medium text-gray-900">Low</th>
          <th class="whitespace-nowrap text-left px-4 py-2 font-medium text-gray-900">Close</th>
          <th class="whitespace-nowrap text-left px-4 py-2 font-medium text-gray-900">Volume</th>
          <th class="whitespace-nowrap text-left px-4 py-2 font-medium text-gray-900">Change</th>
        </tr>
      </thead>

      <tbody class="divide-y divide-gray-200">
        @foreach ($records as $record)
        @php
            $textColor = match (true) {
                isset($record['change']) && $record['change'] < 0 => "text-red-500",
                !isset($record['change']) => "text-gray-500",
                default => "text-green-500",
            };
        @endphp
        <tr>
            <td class="whitespace-nowrap px-4 py-2 font-medium {{ $textColor }}">{{ $record['symbol'] ?? '---' }}</td>
            <td class="whitespace-nowrap px-4 py-2 font-medium {{ $textColor }}">{{ $record['name'] ?? '---' }}</td>
            <td class="whitespace-nowrap px-4 py-2 font-medium {{ $textColor }}">{{ $record['high'] ?? '---' }}</td>
            <td class="whitespace-nowrap px-4 py-2 font-medium {{ $textColor }}">{{ $record['low'] ?? '---' }}</td>
            <td class="whitespace-nowrap px-4 py-2 font-medium {{ $textColor }}">{{ $record['close'] ?? '---' }}</td>
            <td class="whitespace-nowrap px-4 py-2 font-medium {{ $textColor }}">{{ $record['volume'] ?? '---' }}</td>
            <td class="whitespace-nowrap px-4 py-2 font-medium {{ $textColor }}">{{ $record['change'] ?? '---' }}</td>
        </tr>
        @endforeach
      </tbody>
    </table>

    <x-custom-paginator :paginator="$records" />
  </div>

  @endsection
