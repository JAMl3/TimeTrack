@if ($requests->isEmpty())
    <div class="text-center py-8">
        <p class="text-gray-500 dark:text-gray-400">No holiday requests found.</p>
    </div>
@else
    <div class="overflow-x-auto">
        <table class="min-w-full divide-y divide-gray-200 dark:divide-gray-700">
            <thead class="bg-gray-50 dark:bg-gray-700">
                <tr>
                    <th
                        class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">
                        Employee
                    </th>
                    <th
                        class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">
                        Start Date
                    </th>
                    <th
                        class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">
                        End Date
                    </th>
                    <th
                        class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">
                        Days
                    </th>
                    <th
                        class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">
                        Status
                    </th>
                    <th
                        class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">
                        Actions
                    </th>
                </tr>
            </thead>
            <tbody class="bg-white dark:bg-gray-800 divide-y divide-gray-200 dark:divide-gray-700">
                @foreach ($requests as $request)
                    <tr>
                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900 dark:text-gray-300">
                            {{ $request->user->name }}
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900 dark:text-gray-300">
                            {{ $request->start_date->format('M d, Y') }}
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900 dark:text-gray-300">
                            {{ $request->end_date->format('M d, Y') }}
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900 dark:text-gray-300">
                            {{ $request->start_date->diffInDaysExcludingWeekends($request->end_date) + 1 }}
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap">
                            <span
                                class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full
                                @if ($request->status === 'approved') bg-green-100 text-green-800 dark:bg-green-900 dark:text-green-300
                                @elseif ($request->status === 'rejected') bg-red-100 text-red-800 dark:bg-red-900 dark:text-red-300
                                @else bg-yellow-100 text-yellow-800 dark:bg-yellow-900 dark:text-yellow-300 @endif">
                                {{ ucfirst($request->status) }}
                            </span>
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm font-medium">
                            @if ($request->status === 'pending' && auth()->user()->can('update', $request))
                                <form action="{{ route('holidays.requests.update-status', $request) }}" method="POST"
                                    class="inline">
                                    @csrf
                                    @method('PATCH')
                                    <input type="hidden" name="status" value="approved">
                                    <button type="submit"
                                        class="text-green-600 dark:text-green-400 hover:text-green-900 dark:hover:text-green-300 mr-3">
                                        Approve
                                    </button>
                                </form>
                                <button type="button" onclick="showRejectModal('{{ $request->id }}')"
                                    class="text-red-600 dark:text-red-400 hover:text-red-900 dark:hover:text-red-300">
                                    Reject
                                </button>
                            @endif
                        </td>
                    </tr>
                @endforeach
            </tbody>
        </table>
    </div>
    <div class="mt-4 px-4">
        {{ $requests->links() }}
    </div>
@endif

<!-- Reject Modal -->
<div id="rejectModal" class="fixed inset-0 bg-gray-600 bg-opacity-50 hidden overflow-y-auto h-full w-full">
    <div class="relative top-20 mx-auto p-5 border w-96 shadow-lg rounded-md bg-white dark:bg-gray-800">
        <div class="mt-3">
            <h3 class="text-lg font-medium text-gray-900 dark:text-gray-100 mb-4">Reject Holiday Request</h3>
            <form id="rejectForm" method="POST">
                @csrf
                @method('PATCH')
                <input type="hidden" name="status" value="rejected">
                <div class="mb-4">
                    <label for="rejection_reason"
                        class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                        Reason for Rejection
                    </label>
                    <textarea name="rejection_reason" id="rejection_reason" rows="3" required
                        class="w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-md shadow-sm focus:outline-none focus:ring-blue-500 focus:border-blue-500 dark:bg-gray-700 dark:text-gray-300"></textarea>
                </div>
                <div class="flex justify-end space-x-3">
                    <button type="button" onclick="hideRejectModal()"
                        class="px-4 py-2 text-sm font-medium text-gray-700 bg-white border border-gray-300 rounded-md hover:bg-gray-50 dark:bg-gray-700 dark:text-gray-300 dark:border-gray-600 dark:hover:bg-gray-600">
                        Cancel
                    </button>
                    <button type="submit"
                        class="px-4 py-2 text-sm font-medium text-white bg-red-600 border border-transparent rounded-md hover:bg-red-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-red-500">
                        Reject
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

<script>
    function showRejectModal(requestId) {
        const modal = document.getElementById('rejectModal');
        const form = document.getElementById('rejectForm');
        form.action = "{{ url('holidays/requests') }}/" + requestId + "/status";
        modal.classList.remove('hidden');
    }

    function hideRejectModal() {
        const modal = document.getElementById('rejectModal');
        modal.classList.add('hidden');
        document.getElementById('rejection_reason').value = '';
    }
</script>
