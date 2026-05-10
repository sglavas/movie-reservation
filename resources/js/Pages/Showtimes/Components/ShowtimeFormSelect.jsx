import { formatSelectText } from '../../../utils/showtimeHelpers';

export default function ShowtimeFormSelect({infoValue, selectArray, setData, field}) {
    return(
        <div>
            <select
                id={field}
                name={field}
                className="col-start-1 row-start-1 w-full appearance-none rounded-md bg-white/5 py-1.5 pl-3 pr-8 text-base text-white outline outline-1 -outline-offset-1 outline-white/10 *:bg-gray-800 focus:outline focus:outline-2 focus:-outline-offset-2 focus:outline-indigo-500 sm:text-sm/6"
                value={infoValue}
                onChange={e => setData(field, e.target.value)}
                required
            >
                {/* Map over the info for the select array and display the elements */}
                {selectArray.map(el => (
                    <option value={el.id} key={el.id}>{formatSelectText(field, el)}</option>
                ))}
            </select>
        </div>
    );
}