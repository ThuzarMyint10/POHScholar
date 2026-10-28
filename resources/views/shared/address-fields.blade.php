@php
  $selectedCountry = old('country', $selectedCountry ?? 'Sri Lanka');
  $selectedDistrict = old('district', $selectedDistrict ?? '');
  $addressDisabled = $addressDisabled ?? false;
@endphp
<div class="form-row address-fields" data-address-fields data-address-options="{{ json_encode($addressOptions) }}">
  <div class="form-group required">
    <label for="country">Country</label>
    <div class="searchable-select" data-searchable-select>
      <input type="text" id="country" name="country" value="{{ $selectedCountry }}" autocomplete="off" required role="combobox" aria-autocomplete="list" aria-expanded="false" aria-controls="country-options" data-combobox-input {{ $addressDisabled ? 'readonly' : '' }}>
      <button type="button" class="searchable-select__toggle" aria-label="Show countries" tabindex="-1" data-combobox-toggle {{ $addressDisabled ? 'disabled' : '' }}>&#9662;</button>
      <div class="searchable-select__options" id="country-options" role="listbox" hidden data-combobox-options></div>
    </div>
  </div>
  <div class="form-group required">
    <label for="district">District</label>
    <div class="searchable-select" data-searchable-select>
      <input type="text" id="district" name="district" value="{{ $selectedDistrict }}" autocomplete="off" required role="combobox" aria-autocomplete="list" aria-expanded="false" aria-controls="district-options" data-combobox-input {{ $addressDisabled ? 'readonly' : '' }}>
      <button type="button" class="searchable-select__toggle" aria-label="Show districts" tabindex="-1" data-combobox-toggle {{ $addressDisabled ? 'disabled' : '' }}>&#9662;</button>
      <div class="searchable-select__options" id="district-options" role="listbox" hidden data-combobox-options></div>
    </div>
    <div class="hint" data-district-hint>Select a country first to see its districts.</div>
  </div>
</div>
<script>
  (() => {
    const wrapper = document.querySelector('[data-address-fields]');
    if (!wrapper) return;

    const options = JSON.parse(wrapper.dataset.addressOptions);
    const country = wrapper.querySelector('#country');
    const district = wrapper.querySelector('#district');
    const hint = wrapper.querySelector('[data-district-hint]');

    function setupCombobox(container, getOptions, onSelect) {
      const input = container.querySelector('[data-combobox-input]');
      const toggle = container.querySelector('[data-combobox-toggle]');
      const list = container.querySelector('[data-combobox-options]');
      let activeIndex = -1;

      function close() {
        list.hidden = true;
        input.setAttribute('aria-expanded', 'false');
        activeIndex = -1;
      }

      function open() {
        if (input.readOnly || input.disabled) return;
        render();
        list.hidden = false;
        input.setAttribute('aria-expanded', 'true');
      }

      function select(value) {
        input.value = value;
        close();
        onSelect(value);
      }

      function render() {
        const available = getOptions();
        const query = available.includes(input.value) ? '' : input.value.trim().toLowerCase();
        const filtered = available.filter((value) => value.toLowerCase().includes(query));
        list.innerHTML = '';
        activeIndex = -1;

        if (!filtered.length) {
          const empty = document.createElement('div');
          empty.className = 'searchable-select__empty';
          empty.textContent = 'No matches found';
          list.appendChild(empty);
          return;
        }

        filtered.forEach((value, index) => {
          const option = document.createElement('button');
          option.type = 'button';
          option.className = 'searchable-select__option';
          option.setAttribute('role', 'option');
          option.textContent = value;
          option.addEventListener('mousedown', (event) => event.preventDefault());
          option.addEventListener('click', () => select(value));
          option.dataset.index = index;
          list.appendChild(option);
        });
      }

      input.addEventListener('focus', open);
      input.addEventListener('click', open);
      input.addEventListener('input', () => {
        open();
        onSelect(input.value);
      });
      input.addEventListener('keydown', (event) => {
        const choices = [...list.querySelectorAll('.searchable-select__option')];
        if (event.key === 'ArrowDown' || event.key === 'ArrowUp') {
          event.preventDefault();
          if (list.hidden) open();
          activeIndex = event.key === 'ArrowDown'
            ? Math.min(activeIndex + 1, choices.length - 1)
            : Math.max(activeIndex - 1, 0);
          choices.forEach((choice, index) => choice.classList.toggle('is-active', index === activeIndex));
          choices[activeIndex]?.scrollIntoView({ block: 'nearest' });
        } else if (event.key === 'Enter' && activeIndex >= 0) {
          event.preventDefault();
          select(choices[activeIndex].textContent);
        } else if (event.key === 'Escape') {
          close();
        }
      });
      toggle.addEventListener('click', () => {
        if (list.hidden) open(); else close();
        input.focus();
      });

      return { close, render };
    }

    const countrySelect = setupCombobox(
      country.closest('[data-searchable-select]'),
      () => Object.keys(options),
      () => updateDistricts()
    );
    const districtSelect = setupCombobox(
      district.closest('[data-searchable-select]'),
      () => options[country.value] || [],
      () => {}
    );

    function updateDistricts() {
      const districts = options[country.value] || [];
      if (district.value && !districts.includes(district.value)) district.value = '';
      district.readOnly = !country.value || districts.length === 0;
      district.closest('[data-searchable-select]').querySelector('[data-combobox-toggle]').disabled = district.readOnly;
      hint.textContent = districts.length
        ? `${districts.length} district${districts.length === 1 ? '' : 's'} available`
        : 'No districts configured for this country yet.';
      districtSelect.render();
    }

    document.addEventListener('click', (event) => {
      wrapper.querySelectorAll('[data-searchable-select]').forEach((container) => {
        if (!container.contains(event.target)) container.querySelector('[data-combobox-options]').hidden = true;
      });
    });

    updateDistricts();
  })();
</script>
