export default function FormInput({field, type, slot, setData, value}){
    return(
            <div className="sm:col-span-3">
              <label htmlFor={field} className="block text-sm/6 font-medium text-white">
                {slot}
              </label>
              <div className="mt-2">
                <input
                  id={field}
                  name={field}
                  type={type}
                  value={value}
                  onChange={e => setData(field, e.target.value)}
                  className="block w-md rounded-md bg-white/5 px-3 py-1.5 text-base text-white outline outline-1 -outline-offset-1 outline-white/10 placeholder:text-gray-500 focus:outline focus:outline-2 focus:-outline-offset-2 focus:outline-indigo-500 sm:text-sm/6"
                  required
                />
              </div>
            </div>
    )
}