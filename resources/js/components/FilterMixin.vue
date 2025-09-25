<script>
import _ from 'lodash';
import moment from 'moment'

export default {
    props: {
        filter: {
            required: true
        },
        value: {
            default: null
        }
    },
    computed: {
        selected: {
            get() {
                if (this.value === null) {
                    switch (this.filter.type) {
                        case 'date':
                            return null;
                        case 'number':
                            return [this.filter.stats.min, this.filter.stats.max];
                        default:
                            return [];
                    }
                }
                if (_.isArray(this.value)) {
                    return _.map(this.value, value => {
                        return _.find(this.filter.options, {value});
                    })
                }

                return this.value;
            },
            set(value) {
                this.update(value)
            }
        }
    },
    methods: {
        update(value) {
            if (value instanceof Date) {
                value = moment(value).format('YYYY-MM-DD');
            } else if (_.isArray(value) && value.length > 0 && typeof value[0] === 'object') {
                value = _.map(value, item => {
                    return (item.hasOwnProperty('value')) ? item.value : item;
                });
            }
            this.$emit('updated', {
                field_code: this.filter.code,
                raw: this.filter.raw,
                selected: value
            });
        },
        order(options) {
            return (this.filter.type === 'array') ?
                _.orderBy(options, ['label','order'], 'asc')
                :
                _.orderBy(options, 'order', 'asc');
        },
        isSelected(selected, value) {
            return !!_.find(selected, {value})
        },
        checkboxChanged(el) {
            if (el.target.checked) {
                this.update([...this.selected, el.target.value])
            } else {
                this.update(_.filter(this.selected, (item) => {
                    return item.value !== el.target.value
                }));
            }
        }
    }
}
</script>
