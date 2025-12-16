@props(['selectName', 'core' => false])
@if (!$core)
    <select name="{{ $selectName }}" id="" class="bg-black rounded-lg px-3 py-2 text-white w-full lg:w-auto">
        <option value="" disabled selected>Select grade</option>
        <option value="A1">A1</option>
        <option value="B2">B2</option>
        <option value="B3">B3</option>
        <option value="C4">C4</option>
        <option value="C5">C5</option>
        <option value="C6">C6</option>
        <option value="D7">D7</option>
        <option value="E8">E8</option>
        <option value="F9">F9</option>
    </select>
@else
    <select name="{{ $selectName }}" id="" class="bg-black rounded-xl px-3 text-white">
        <option value="A1">A1</option>
        <option value="B2">B2</option>
        <option value="B3">B3</option>
        <option value="C4">C4</option>
        <option value="C5">C5</option>
        <option value="C6">C6</option>
        <option value="D7">D7</option>
        <option value="E8">E8</option>
        <option value="F9">F9</option>
    </select>
@endif
