 @props(['header', 'name', 'selectName', 'electives', 'className'])

 <div class="flex flex-col gap-3 items-start justify-start w-full rounded-lg p-3 border border-gray-700">
     <div class="flex items-center justify-between w-full p-2 text-white rounded-lg">
         <p class="text-xl font-bold">{{ $header }}</p>
     </div>
     <div class="flex flex-col lg:flex-row items-start gap-3 justify-start w-full">
         <select class="w-full bg-neutral-900 rounded-lg text-white px-3 py-2 space-y-2 {{ $className }}" name="{{ $name }}">
             <option value="" disabled selected>Select elective</option>
             @foreach ($electives as $elective)
                 <option value="{{ $elective->value }}">{{ $elective->value }}</option>
             @endforeach

         </select>
         <x-grade-selector-component :selectName="$selectName" />
     </div>
 </div>
