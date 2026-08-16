export function datePicker() {
    return {
        open: false,
        value: '',
        cursor: new Date(),
        months: [
            'January', 'February', 'March', 'April', 'May', 'June',
            'July', 'August', 'September', 'October', 'November', 'December',
        ],
        weekdays: ['Su', 'Mo', 'Tu', 'We', 'Th', 'Fr', 'Sa'],

        init() {
            if (this.value) {
                this.cursor = this.parse(this.value);
            }

            this.$watch('value', (val) => {
                if (val) {
                    this.cursor = this.parse(val);
                }
            });
        },

        parse(iso) {
            const [year, month, day] = iso.split('-').map(Number);

            return new Date(year, month - 1, day);
        },

        format(date) {
            const year = date.getFullYear();
            const month = String(date.getMonth() + 1).padStart(2, '0');
            const day = String(date.getDate()).padStart(2, '0');

            return `${year}-${month}-${day}`;
        },

        get display() {
            if (! this.value) {
                return '';
            }

            return this.parse(this.value).toLocaleDateString(undefined, {
                month: 'short',
                day: 'numeric',
                year: 'numeric',
            });
        },

        get title() {
            return `${this.months[this.cursor.getMonth()]} ${this.cursor.getFullYear()}`;
        },

        get cells() {
            const year = this.cursor.getFullYear();
            const month = this.cursor.getMonth();
            const startPad = new Date(year, month, 1).getDay();
            const daysInMonth = new Date(year, month + 1, 0).getDate();
            const cells = [];

            for (let i = 0; i < startPad; i++) {
                cells.push(null);
            }

            for (let day = 1; day <= daysInMonth; day++) {
                cells.push(new Date(year, month, day));
            }

            return cells;
        },

        prev() {
            this.cursor = new Date(this.cursor.getFullYear(), this.cursor.getMonth() - 1, 1);
        },

        next() {
            this.cursor = new Date(this.cursor.getFullYear(), this.cursor.getMonth() + 1, 1);
        },

        pick(date) {
            this.value = this.format(date);
            this.open = false;
        },

        clear() {
            this.value = '';
            this.open = false;
        },

        isSelected(date) {
            return Boolean(date && this.value === this.format(date));
        },

        isToday(date) {
            return Boolean(date && this.format(date) === this.format(new Date()));
        },
    };
}
