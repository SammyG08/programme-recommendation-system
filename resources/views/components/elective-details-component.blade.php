 @props(['header', 'inputName', 'selectName'])

 <div class="flex flex-col gap-3 items-start justify-start w-full rounded-lg p-3 border border-gray-700">
     <div class="flex items-center justify-between w-full p-2 text-white rounded-lg">
         <p class="text-xl font-bold">{{ $header }}</p>
     </div>
     <div class="flex flex-col lg:flex-row items-start gap-3 justify-start w-full">
         <input type="text" class="w-full bg-neutral-900 rounded-lg text-white px-3 py-2" placeholder="Course name"
             name="{{ $inputName }}">
         <x-grade-selector-component :selectName="$selectName" />
     </div>
 </div>
